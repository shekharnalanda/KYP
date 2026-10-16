#!/usr/bin/env python3
"""Install public course pages while preserving unrelated server customizations."""
import datetime
import hashlib
import shutil
import subprocess
import sys
from pathlib import Path

ROOT = Path(sys.argv[1]) if len(sys.argv) > 1 else Path('/home4/mcied45x/repositories/KYP')
STAGE = Path(__file__).resolve().parent.parent
PHP = '/opt/cpanel/ea-php83/root/usr/bin/php'
BACKUP = Path('/home4/mcied45x/kyp-deploy-backups') / ('public-guide-' + datetime.datetime.utcnow().strftime('%Y%m%d-%H%M%S'))
files = [
    'app/Http/Controllers/PublicCourseController.php', 'config/kyp-public.php',
    'public/css/program-guide.css', 'resources/views/home.blade.php',
    'resources/views/components/public-site-header.blade.php',
    'resources/views/components/public-site-footer.blade.php',
    'resources/views/components/public-course-card.blade.php',
    'resources/views/components/public-study-plan.blade.php',
    'resources/views/public/courses/show.blade.php',
    'scripts/install-public-course-guide.py',
]
changes = {name: (STAGE / name).read_text() for name in files}
home = (ROOT / 'resources/views/home.blade.php').read_bytes()
if b'program-guide.css' not in home and hashlib.sha256(home).hexdigest() != '1f4a87ca250760071fddc1848c77fd746108fe40d9257a5f613edac3937b7568':
    raise RuntimeError('Live homepage changed; review before overwriting.')

name = 'resources/views/layouts/app.blade.php'
layout = (ROOT / name).read_text()
if "@stack('head')" not in layout:
    if '</head>' not in layout:
        raise RuntimeError('Live head needs review.')
    layout = layout.replace('</head>', "@stack('head')\n</head>", 1)
changes[name] = layout

name = 'routes/web.php'
routes = (ROOT / name).read_text()
anchor = "Route::view('/', 'home')->name('home');"
replacement = "Route::get('/', [\\App\\Http\\Controllers\\PublicCourseController::class, 'index'])->name('home');\nRoute::get('/courses/{slug}', [\\App\\Http\\Controllers\\PublicCourseController::class, 'show'])\n    ->where('slug', 'cit|cls|css|ai-dm')->name('public.courses.show');"
if "name('public.courses.show')" not in routes:
    if anchor not in routes:
        raise RuntimeError('Live homepage route changed; review before deployment.')
    routes = routes.replace(anchor, replacement, 1)
changes[name] = routes

BACKUP.mkdir(parents=True)
originals = {}
try:
    for name, content in changes.items():
        target = ROOT / name
        originals[name] = target.read_bytes() if target.exists() else None
        if target.exists():
            dest = BACKUP / name
            dest.parent.mkdir(parents=True, exist_ok=True)
            shutil.copy2(str(target), str(dest))
        target.parent.mkdir(parents=True, exist_ok=True)
        target.write_text(content)
    for name in changes:
        if name.endswith('.php'):
            subprocess.check_call([PHP, '-l', str(ROOT / name)])
    for operation in ['config:clear', 'route:clear', 'view:clear']:
        subprocess.check_call([PHP, 'artisan', operation, '--no-interaction'], cwd=str(ROOT))
    smoke = '''require 'vendor/autoload.php'; $app = require 'bootstrap/app.php'; $kernel = $app->make(Illuminate\\Contracts\\Http\\Kernel::class); foreach (['/', '/courses/cit', '/courses/cls', '/courses/css', '/courses/ai-dm', '/login', '/admission', '/enquiry'] as $path) { $request = Illuminate\\Http\\Request::create('https://kyp.mciedu.com'.$path); $response = $kernel->handle($request); if ($response->getStatusCode() !== 200) { fwrite(STDERR, "Public page failed: ".$path." status ".$response->getStatusCode()."\\n"); exit(1); } echo "PAGE_OK ".$path."\\n"; $kernel->terminate($request, $response); }'''
    subprocess.check_call([PHP, '-r', smoke], cwd=str(ROOT))
except Exception:
    for name, original in originals.items():
        target = ROOT / name
        if original is None:
            if target.exists():
                target.unlink()
        else:
            target.write_bytes(original)
    for operation in ['config:clear', 'route:clear', 'view:clear']:
        subprocess.run([PHP, 'artisan', operation, '--no-interaction'], cwd=str(ROOT), check=False)
    raise
print('KYP_PUBLIC_GUIDE_INSTALLED', len(changes), 'files; backup:', BACKUP)
