<?php

namespace App\Console\Commands;

use App\Models\Video;
use Illuminate\Console\Command;

class ProcessVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:videos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates multiple resolutions and generates thumbnails of ALL unprocessed videos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $videos = Video::whereNull('processed_at')->get();

        foreach ($videos as $video) {
            $video->process();
        }

        $this->info('Successfully processed ' . count($videos) . ' videos.');

        return 0;
    }
}
