"""Generate PIKO POP brand PNG assets for Phase 7."""
from PIL import Image, ImageDraw, ImageFont
import os

OUT = os.path.join(os.path.dirname(__file__), '..', 'user_assets', 'images')
os.makedirs(OUT, exist_ok=True)

# Brand colors
PINK = (255, 79, 163)
PURPLE = (108, 60, 201)
YELLOW = (255, 216, 61)
BLUE = (32, 189, 247)
BG = (255, 249, 253)
LIGHT = (255, 240, 248)
DARK = (61, 43, 94)
WHITE = (255, 255, 255)


def load_font(size, bold=False):
    candidates = [
        'C:/Windows/Fonts/arialbd.ttf' if bold else 'C:/Windows/Fonts/arial.ttf',
        'C:/Windows/Fonts/segoeuib.ttf' if bold else 'C:/Windows/Fonts/segoeui.ttf',
        '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf' if bold else '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
    ]
    for path in candidates:
        if os.path.exists(path):
            return ImageFont.truetype(path, size)
    return ImageFont.load_default()


def draw_logo(filename, on_dark=False):
    w, h = 480, 112
    img = Image.new('RGBA', (w, h), (0, 0, 0, 0))
    draw = ImageDraw.Draw(img)
    font = load_font(52, bold=True)
    small = load_font(18, bold=True)

    if on_dark:
        draw.text((8, 18), 'PIKO', font=font, fill=WHITE)
        bbox = draw.textbbox((8, 18), 'PIKO', font=font)
        draw.text((bbox[2] + 6, 18), 'POP', font=font, fill=YELLOW)
    else:
        draw.text((8, 18), 'PIKO', font=font, fill=PINK)
        bbox = draw.textbbox((8, 18), 'PIKO', font=font)
        draw.text((bbox[2] + 6, 18), 'POP', font=font, fill=PURPLE)

    # Decorative dots
    dots = [(400, 22, YELLOW, 10), (430, 42, BLUE, 7), (412, 68, PINK, 5)]
    for x, y, c, r in dots:
        draw.ellipse((x - r, y - r, x + r, y + r), fill=c + (255,))

    img.save(os.path.join(OUT, filename), 'PNG')
    print('Created', filename)


def draw_favicon():
    size = 64
    img = Image.new('RGBA', (size, size), (0, 0, 0, 0))
    draw = ImageDraw.Draw(img)
    draw.rounded_rectangle((2, 2, size - 2, size - 2), radius=14, fill=PINK)
    font = load_font(28, bold=True)
    draw.text((10, 14), 'P', font=font, fill=WHITE)
    img.save(os.path.join(OUT, 'piko-pop-favicon.png'), 'PNG')
    print('Created piko-pop-favicon.png')


def pastel_bg(draw, w, h):
    for y in range(h):
        t = y / max(h - 1, 1)
        r = int(LIGHT[0] * (1 - t) + BG[0] * t)
        g = int(LIGHT[1] * (1 - t) + BG[1] * t)
        b = int(LIGHT[2] * (1 - t) + BG[2] * t)
        draw.line([(0, y), (w, y)], fill=(r, g, b))


def draw_blobs(draw, w, h):
    blobs = [
        (int(w * 0.12), int(h * 0.2), int(w * 0.22), PINK + (60,)),
        (int(w * 0.72), int(h * 0.15), int(w * 0.18), PURPLE + (50,)),
        (int(w * 0.55), int(h * 0.65), int(w * 0.16), YELLOW + (70,)),
        (int(w * 0.2), int(h * 0.72), int(w * 0.12), BLUE + (60,)),
    ]
    for cx, cy, radius, color in blobs:
        draw.ellipse((cx - radius, cy - radius, cx + radius, cy + radius), fill=color)


def draw_stars(draw, w, h):
    stars = ['✦', '★', '✨', '♥', '☆']
    positions = [(0.08, 0.12), (0.88, 0.18), (0.78, 0.75), (0.15, 0.82), (0.5, 0.08)]
    font = load_font(36)
    small = load_font(24)
    fonts = [font, small, font, small, font]
    colors = [YELLOW, PINK, BLUE, PURPLE, YELLOW]
    for i, ((px, py), f, c) in enumerate(zip(positions, fonts, colors)):
        draw.text((int(w * px), int(h * py)), stars[i], font=f, fill=c)


def banner_text(draw, w, h, title, subtitle):
    title_font = load_font(max(36, w // 22), bold=True)
    sub_font = load_font(max(20, w // 38))

    draw.ellipse((int(w * 0.06), int(h * 0.18), int(w * 0.14), int(h * 0.34)), fill=YELLOW + (200,))

    draw.text((int(w * 0.16), int(h * 0.28)), title, font=title_font, fill=DARK)
    draw.text((int(w * 0.16), int(h * 0.52)), subtitle, font=sub_font, fill=(122, 107, 142))


def hero_desktop():
    w, h = 1200, 520
    img = Image.new('RGB', (w, h), BG)
    draw = ImageDraw.Draw(img)
    pastel_bg(draw, w, h)
    draw_blobs(draw, w, h)
    draw.rounded_rectangle((w - 420, 60, w - 60, h - 60), radius=40, fill=WHITE + (230,))
    draw.text((w - 390, 120), 'Stickers', font=load_font(34, bold=True), fill=PINK)
    draw.text((w - 390, 175), 'Toys', font=load_font(34, bold=True), fill=PURPLE)
    draw.text((w - 390, 230), 'Gifts', font=load_font(34, bold=True), fill=BLUE)
    for i, c in enumerate([PINK, YELLOW, BLUE, PURPLE]):
        draw.ellipse((w - 350 + i * 55, 310, w - 310 + i * 55, 350), fill=c)
    banner_text(draw, w, h, 'Little Things. Big Smiles.', 'Cute stickers, stationery & toys for happy kids')
    img.save(os.path.join(OUT, 'hero-banner-desktop.png'), 'PNG')
    print('Created hero-banner-desktop.png')


def hero_mobile():
    w, h = 800, 560
    img = Image.new('RGB', (w, h), BG)
    draw = ImageDraw.Draw(img)
    pastel_bg(draw, w, h)
    draw_blobs(draw, w, h)
    banner_text(draw, w, h, 'PIKO POP', 'Shop cute finds today')
    draw.rounded_rectangle((40, h - 180, w - 40, h - 40), radius=30, fill=WHITE)
    draw.text((70, h - 150), 'Stickers · Stationery · Toys', font=load_font(24, bold=True), fill=DARK)
    img.save(os.path.join(OUT, 'hero-banner-mobile.png'), 'PNG')
    print('Created hero-banner-mobile.png')


def about_hero():
    w, h = 800, 800
    img = Image.new('RGB', (w, h), LIGHT)
    draw = ImageDraw.Draw(img)
    draw_blobs(draw, w, h)
    draw.rounded_rectangle((80, 120, w - 80, h - 120), radius=50, fill=WHITE)
    draw.text((120, 180), 'Our Story', font=load_font(48, bold=True), fill=DARK)
    draw.text((120, 260), 'Small things that', font=load_font(32), fill=(122, 107, 142))
    draw.text((120, 260), 'create big smiles', font=load_font(32), fill=(122, 107, 142))
    for i, (label, color) in enumerate([
        ('Stickers', PINK), ('Stationery', PURPLE), ('Toys', BLUE), ('Gifts', YELLOW)
    ]):
        y = 420 + i * 70
        draw.rounded_rectangle((120, y, 360, y + 48), radius=24, fill=color + (40,))
        draw.text((140, y + 10), label, font=load_font(24, bold=True), fill=color)
    img.save(os.path.join(OUT, 'about-hero.png'), 'PNG')
    print('Created about-hero.png')


def party_banner():
    w, h = 1200, 420
    img = Image.new('RGB', (w, h), BG)
    draw = ImageDraw.Draw(img)
    pastel_bg(draw, w, h)
    draw_blobs(draw, w, h)
    banner_text(draw, w, h, 'Party & Gift Orders', 'Birthday return gifts · School events · Custom combos')
    draw.rounded_rectangle((w - 340, 80, w - 80, h - 80), radius=35, fill=PURPLE + (30,))
    draw.text((w - 310, 130), 'Party Packs', font=load_font(30, bold=True), fill=PURPLE)
    draw.text((w - 310, 190), 'Return Gifts', font=load_font(26), fill=DARK)
    draw.text((w - 310, 240), 'Custom Combos', font=load_font(26), fill=DARK)
    img.save(os.path.join(OUT, 'party-gift-banner.png'), 'PNG')
    print('Created party-gift-banner.png')


def product_fallback():
    size = 600
    img = Image.new('RGB', (size, size), LIGHT)
    draw = ImageDraw.Draw(img)
    draw_blobs(draw, size, size)
    draw.rounded_rectangle((80, 80, size - 80, size - 80), radius=40, fill=WHITE)
    draw.text((size // 2 - 90, size // 2 - 70), 'PIKO', font=load_font(72, bold=True), fill=PINK)
    draw.text((size // 2 - 60, size // 2 + 10), 'POP', font=load_font(72, bold=True), fill=PURPLE)
    draw.text((size // 2 - 110, size // 2 + 100), 'Cute product image', font=load_font(22), fill=(122, 107, 142))
    img.save(os.path.join(OUT, 'product-fallback.png'), 'PNG')
    print('Created product-fallback.png')


if __name__ == '__main__':
    draw_logo('piko-pop-logo.png', on_dark=False)
    draw_logo('piko-pop-logo-white.png', on_dark=True)
    draw_favicon()
    hero_desktop()
    hero_mobile()
    about_hero()
    party_banner()
    product_fallback()
