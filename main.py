import telebot
from telebot.types import InlineKeyboardMarkup, InlineKeyboardButton
import random
import os

TOKEN = os.getenv("TG_TOKEN") or "8142450147:AAHwAD_xdWLntO9GAhnj7QreLHh5sN9M5YE"
bot = telebot.TeleBot(TOKEN, parse_mode="HTML")

# ======== O'yin holati saqlash (xotirada) ========
games = {}  # chat_id -> state dict: {'board':..., 'turn':'w', 'selected':(r,c) or None}

# ======== Dastlabki taxta ========
def initial_board():
    # '.' bo'sh, 'w' oq, 'b' qora, 'W' oq damka, 'B' qora damka
    board = [['.' for _ in range(8)] for _ in range(8)]
    for r in range(3):
        for c in range(8):
            if (r + c) % 2 == 1:
                board[r][c] = 'b'
    for r in range(5, 8):
        for c in range(8):
            if (r + c) % 2 == 1:
                board[r][c] = 'w'
    return board

# ======== Render (matn + tugmalar) ========
def piece_char(cell):
    return {'w': '⚪', 'W': '♔', 'b': '⚫', 'B': '♚', '.': '·'}[cell]

def board_text(board, turn):
    header = "<b>Shashka</b> — Siz: ⚪  |  Bot: ⚫\n"
    header += "Navbat: " + ("Sizda" if turn == 'w' else "Botda") + "\n"
    header += "Fayllar: a b c d e f g h (pastdan yuqoriga 1..8)\n\n"
    rows = []
    for r in range(7, -1, -1):
        row = []
        for c in range(8):
            ch = piece_char(board[r][c]) if (r+c)%2==1 else ' '
            row.append(ch)
        rows.append(f"{r+1}  " + " ".join(row))
    footer = "\n   a b c d e f g h"
    return header + "\n".join(rows) + footer

def coord_to_alg(r, c):
    return "abcdefgh"[c] + str(r+1)

def make_keyboard(board, selectable=None, highlights=None):
    # selectable: qaysi hujayralar bosilishi mumkin (set of (r,c))
    # highlights: ko'rsatiladigan manzillar (set of (r,c))
    kb = InlineKeyboardMarkup()
    for r in range(7, -1, -1):
        row_btns = []
        for c in range(8):
            cell = board[r][c]
            label = piece_char(cell) if (r+c)%2==1 else ' '
            data = "noop"
            if selectable and (r, c) in selectable:
                label = "▣"  # tanlash mumkin
                data = f"sel:{r}:{c}"
            elif highlights and (r, c) in highlights:
                label = "●"
                data = f"to:{r}:{c}"
            row_btns.append(InlineKeyboardButton(label, callback_data=data))
        kb.row(*row_btns)
    # Pastga boshqaruv tugmalari
    kb.row(
        InlineKeyboardButton("♻️ Yangi o‘yin", callback_data="restart"),
        InlineKeyboardButton("❓ Qoidalar", callback_data="help")
    )
    return kb

# ======== Qonuniy yurishlarni hisoblash ========
def inside(r, c): return 0 <= r < 8 and 0 <= c < 8

def dirs_for(piece):
    if piece in ('w', 'W'):
        return [(-1, -1), (-1, 1)] if piece == 'w' else [(-1,-1),(-1,1),(1,-1),(1,1)]
    if piece in ('b', 'B'):
        return [(1, -1), (1, 1)] if piece == 'b' else [(-1,-1),(-1,1),(1,-1),(1,1)]

def enemy(color): return 'b' if color == 'w' else 'w'

def is_enemy(cell, color):
    return (cell.lower() == enemy(color)) if cell != '.' else False

def clone(board):
    return [row[:] for row in board]

def promote(board):
    for c in range(8):
        if board[0][c] == 'w': board[0][c] = 'W'
        if board[7][c] == 'b': board[7][c] = 'B'

def find_captures_from(board, r, c, color):
    # Ko'p martalik olish: DFS
    piece = board[r][c]
    caps = []

    def dfs(bd, rr, cc, path, eaten):
        found = False
        for dr, dc in dirs_for(piece if piece in ('W','B') else bd[rr][cc]):
            mr, mc = rr + dr, cc + dc
            tr, tc = rr + 2*dr, cc + 2*dc
            if inside(tr, tc) and inside(mr, mc) and is_enemy(bd[mr][mc], color) and bd[tr][tc] == '.' and (mr,mc) not in eaten:
                found = True
                nb = clone(bd)
                nb[tr][tc] = nb[rr][cc]
                nb[rr][cc] = '.'
                nb[mr][mc] = '.'
                new_eaten = eaten | {(mr,mc)}
                # Upgrade temporary (promotion mid-sequence is allowed at the end)
                tmp_piece = nb[tr][tc]
                if color == 'w' and tr == 0 and tmp_piece == 'w': nb[tr][tc] = 'W'
                if color == 'b' and tr == 7 and tmp_piece == 'b': nb[tr][tc] = 'B'
                dfs(nb, tr, tc, path + [(tr, tc)], new_eaten)
        if not found:
            caps.append(((r, c), path, eaten))

    dfs(board, r, c, [], set())
    # caps: list of ((src), [seq of landings], eaten set). Filter at least one jump
    caps = [m for m in caps if len(m[1]) > 0]
    return caps

def find_simple_moves_from(board, r, c, color):
    piece = board[r][c]
    moves = []
    for dr, dc in dirs_for(piece):
        tr, tc = r + dr, c + dc
        if inside(tr, tc) and board[tr][tc] == '.':
            moves.append(((r, c), (tr, tc)))
    return moves

def legal_moves(board, color):
    jumps = []
    simples = []
    for r in range(8):
        for c in range(8):
            if board[r][c].lower() == color:
                caps = find_captures_from(board, r, c, color)
                if caps: jumps.extend(caps)
                else:
                    simples.extend(find_simple_moves_from(board, r, c, color))
    if jumps:
        return {'type': 'capture', 'moves': jumps}
    return {'type': 'simple', 'moves': simples}

def apply_move(board, move, color):
    nb = clone(board)
    if isinstance(move[1], list):  # capture chain
        (sr, sc), path, eaten = move
        cur_r, cur_c = sr, sc
        for (tr, tc) in path:
            dr = (tr - cur_r) // 2
            dc = (tc - cur_c) // 2
            nb[tr][tc] = nb[cur_r][cur_c]
            nb[cur_r][cur_c] = '.'
            nb[cur_r + dr][cur_c + dc] = '.'
            # Promote mid-chain if landed on promotion row
            tmp_piece = nb[tr][tc]
            if color == 'w' and tr == 0 and tmp_piece == 'w': nb[tr][tc] = 'W'
            if color == 'b' and tr == 7 and tmp_piece == 'b': nb[tr][tc] = 'B'
            cur_r, cur_c = tr, tc
        promote(nb)  # Final promote if needed
    else:
        (sr, sc), (tr, tc) = move
        nb[tr][tc] = nb[sr][sc]
        nb[sr][sc] = '.'
        promote(nb)
    return nb

def has_any_pieces(board, color):
    return any(board[r][c].lower() == color for r in range(8) for c in range(8))

# ======== Botning yurishi ========
def bot_make_move(board):
    lm = legal_moves(board, 'b')
    choice = random.choice(lm['moves'])
    nb = apply_move(board, choice, 'b')
    desc = "Bot yurdi."
    return nb, desc

# ======== Telegram handlerlar ========
@bot.message_handler(commands=['start', 'new', 'play'])
def start_cmd(m):
    chat_id = m.chat.id
    games[chat_id] = {
        'board': initial_board(),
        'turn': 'w',
        'selected': None
    }
    b = games[chat_id]['board']
    lm = legal_moves(b, 'w')
    selectable = set(mv[0] for mv in lm['moves'])
    text = board_text(b, 'w')
    kb = make_keyboard(b, selectable=selectable)
    bot.send_message(chat_id, text, reply_markup=kb)

@bot.callback_query_handler(func=lambda c: True)
def on_cb(c):
    chat_id = c.message.chat.id
    if chat_id not in games:
        bot.answer_callback_query(c.id, "O‘yin topilmadi. /start bosing.")
        return
    state = games[chat_id]
    board = state['board']

    if c.data == "restart":
        start_cmd(c.message)
        bot.answer_callback_query(c.id, "Yangi o‘yin boshlandi.")
        return
    if c.data == "help":
        bot.answer_callback_query(c.id, "Majburiy olish mavjud. Oq sizniki. Tugmalar orqali tanlang: ▣ — tanlash, ● — borish.")
        return
    if c.data == "noop":
        bot.answer_callback_query(c.id)
        return

    # Foydalanuvchi navbati
    if state['turn'] != 'w':
        bot.answer_callback_query(c.id, "Bot navbatda, kuting…")
        return

    lm = legal_moves(board, 'w')

    if c.data.startswith("sel:"):
        _, rs, cs = c.data.split(":")
        r, c_ = int(rs), int(cs)  # renamed to avoid conflict with c
        # Tanlangan manba bo'yicha borish mumkin joylarni topish
        dests = set()
        if lm['type'] == 'capture':
            for mv in lm['moves']:
                (sr, sc), path, eaten = mv
                if (sr, sc) == (r, c_) and path:
                    dests.add(path[0])
        else:
            for mv in lm['moves']:
                (sr, sc), (tr, tc) = mv
                if (sr, sc) == (r, c_):
                    dests.add((tr, tc))
        state['selected'] = (r, c_)
        text = board_text(board, 'w')
        kb = make_keyboard(board, highlights=dests)
        bot.edit_message_text(text, chat_id, c.message.message_id, reply_markup=kb)
        bot.answer_callback_query(c.id, coord_to_alg(r, c_)+" tanlandi")
        return

    if c.data.startswith("to:") and state['selected']:
        _, rr, cc = c.data.split(":")
        tr, tc = int(rr), int(cc)
        sr, sc = state['selected']

        chosen = None
        if lm['type'] == 'capture':
            # mos keluvchi capture topamiz (dastlabki qadam koordinatasi bo'yicha)
            for mv in lm['moves']:
                (r0, c0), path, eaten = mv
                if (r0, c0) == (sr, sc) and path and path[0] == (tr, tc):
                    chosen = mv
                    break
        else:
            for mv in lm['moves']:
                (r0, c0), (r1, c1) = mv
                if (r0, c0) == (sr, sc) and (r1, c1) == (tr, tc):
                    chosen = mv
                    break

        if not chosen:
            bot.answer_callback_query(c.id, "Bu yurish mumkin emas.")
            return

        # Foydalanuvchi yuradi
        board = apply_move(board, chosen, 'w')
        state['board'] = board
        state['selected'] = None

        # Tekshiruv: botning toshi qolganmi?
        if not has_any_pieces(board, 'b'):
            text = board_text(board, 'w') + "\n\n🎉 Tabriklaymiz! Bot toshsiz qoldi — siz yutdingiz!"
            kb = make_keyboard(board)
            bot.edit_message_text(text, chat_id, c.message.message_id, reply_markup=kb)
            bot.answer_callback_query(c.id)
            return

        # Tekshiruv: bot yurishi bormi?
        lm_bot = legal_moves(board, 'b')
        if not lm_bot['moves']:
            text = board_text(board, 'w') + "\n\n🎉 Tabriklaymiz! Botda yurish yo'q — siz yutdingiz!"
            kb = make_keyboard(board)
            bot.edit_message_text(text, chat_id, c.message.message_id, reply_markup=kb)
            bot.answer_callback_query(c.id)
            return

        # Navbat botga
        state['turn'] = 'b'
        text = board_text(board, 'b')
        kb = make_keyboard(board)
        bot.edit_message_text(text, chat_id, c.message.message_id, reply_markup=kb)
        bot.answer_callback_query(c.id)

        # Bot yuradi
        nb, _desc = bot_make_move(board)
        state['board'] = nb

        # Tekshiruv: foydalanuvchi toshi qolmaganmi?
        endnote = ""
        if not has_any_pieces(nb, 'w'):
            endnote = "\n\n😔 Sizning toshlaringiz qolmadi — bot yutdi."

        # Tekshiruv: foydalanuvchi yurishi bormi?
        lm_player = legal_moves(nb, 'w')
        if not lm_player['moves']:
            endnote = "\n\n😔 Sizda yurish yo'q — bot yutdi."

        state['turn'] = 'w'
        text = board_text(nb, 'w') + endnote
        selectable = set(mv[0] for mv in lm_player['moves']) if lm_player['moves'] else set()
        kb = make_keyboard(nb, selectable=selectable)
        bot.edit_message_text(text, chat_id, c.message.message_id, reply_markup=kb)
        return

    bot.answer_callback_query(c.id)

# ======== Run (polling) ========
if __name__ == "__main__":
    print("Bot ishga tushdi...")
    # Agar webhook ilgari yoqilgan bo'lsa, bu yerda ham o'chirib yuborish foydali:
    try:
        import requests
        requests.get(f"https://api.telegram.org/bot{TOKEN}/deleteWebhook", timeout=5)
    except Exception:
        pass
    bot.infinity_polling(skip_pending=True)
