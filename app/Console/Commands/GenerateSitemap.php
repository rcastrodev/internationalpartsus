<?php

namespace App\Console\Commands;

use App\Supplie;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateSitemap extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    const START_SITEMAP_INDEX = '<?xml version="1.0" encoding="UTF-8"?>
    <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    const END_SITEMAP_INDEX    = '</sitemapindex>';

    const START_SITEMAP  = '<?xml version="1.0" encoding="UTF-8"?>
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">';
    const END_SITEMAP    = '</urlset>';
    const AMOUNT_OF_URLS = 5000;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->create();
    }

    private function createSitemap($i, $supplies)
    {
        $content    = self::START_SITEMAP;
        $date           = Carbon::now()->format('Y-m-d');

        foreach ($supplies as $supply) {
            if (! is_object($supply->manufacturer)) continue;
            if (! $supply->manufacturer->slug || ! $supply->slug) continue;

            $url      = env('APP_URL')."/f/{$supply->manufacturer->slug}/p/{$supply->slug}";
            $content .= "<url>
                <loc>$url</loc>
                <lastmod>".$date."</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.8</priority>
            </url>";
        }
        $content .= self::END_SITEMAP;
        Storage::disk('public')->put("sitemap{$i}.xml", $content);
    }

    private function create()
    {
        $supplies       = Supplie::whereNotNull('slug')->distinct()->orderBy('created_at', 'DESC')->get();
        $contentIndex   = self::START_SITEMAP_INDEX;
    
        foreach ($supplies->chunk(self::AMOUNT_OF_URLS) as $k => $arrSupplies) {
            $i = ++$k;
            $contentIndex .= "<sitemap>
                <loc>".env('APP_URL').'/sitemap'.$i.'.xml'."</loc>
            </sitemap>";

            $this->createSitemap($i ,$arrSupplies);
        }

        $contentIndex .= self::END_SITEMAP_INDEX;
        Storage::disk('public')->put("sitemap.xml", $contentIndex);
    }
}
