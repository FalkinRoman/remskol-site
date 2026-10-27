<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class ExportStatic extends Command
{
    protected $signature = 'site:export
        {--base=https://falkinroman.github.io/remskol-site : Публичный адрес, на котором будет лежать статика}
        {--out=docs : Папка для статики}';

    protected $description = 'Статическая демо-версия сайта для GitHub Pages (noindex, форма без отправки)';

    private const ORIGIN = 'http://static-export.local';

    public function handle(Kernel $kernel): int
    {
        $base = rtrim($this->option('base'), '/');
        $out = base_path($this->option('out'));

        config(['site.static_export' => true, 'app.url' => self::ORIGIN]);
        View::share('site', config('site'));

        File::deleteDirectory($out);
        File::ensureDirectoryExists($out);

        $paths = ['/', '/primery-rabot', '/ceny', '/kontakty', '/privacy', '/consent', '/sitemap.xml'];
        foreach (array_keys(config('catalog.services')) as $slug) {
            $paths[] = '/uslugi/'.$slug;
        }

        foreach ($paths as $path) {
            $html = $this->render($kernel, $path, $status);
            if ($status !== 200) {
                $this->error("{$path}: HTTP {$status}");

                return self::FAILURE;
            }

            $file = str_contains(basename($path), '.') ? $out.$path : rtrim($out.$path, '/').'/index.html';
            File::ensureDirectoryExists(dirname($file));
            File::put($file, $this->rebase($html, $base));
            $this->line("  {$path}");
        }

        File::put($out.'/404.html', $this->rebase($this->render($kernel, '/__not-found__', $status), $base));
        File::put($out.'/robots.txt', "User-agent: *\nDisallow: /\n");
        File::put($out.'/.nojekyll', '');
        File::put($out.'/site.webmanifest', str_replace('"/', '"./', File::get(public_path('site.webmanifest'))));

        foreach (['css', 'js', 'images'] as $dir) {
            File::copyDirectory(public_path($dir), $out.'/'.$dir);
        }
        foreach (File::files(public_path()) as $f) {
            if (! in_array($f->getFilename(), ['index.php', '.htaccess', 'robots.txt', 'site.webmanifest'], true)) {
                File::copy($f->getPathname(), $out.'/'.$f->getFilename());
            }
        }

        $this->info("Готово: {$out} → {$base}/");

        return self::SUCCESS;
    }

    private function render(Kernel $kernel, string $path, ?int &$status): string
    {
        $request = Request::create(self::ORIGIN.$path);
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);
        $status = $response->getStatusCode();

        return (string) $response->getContent();
    }

    private function rebase(string $html, string $base): string
    {
        return str_replace(
            [self::ORIGIN, str_replace('/', '\/', self::ORIGIN), parse_url(self::ORIGIN, PHP_URL_HOST)],
            [$base, str_replace('/', '\/', $base), parse_url($base, PHP_URL_HOST)],
            $html,
        );
    }
}
