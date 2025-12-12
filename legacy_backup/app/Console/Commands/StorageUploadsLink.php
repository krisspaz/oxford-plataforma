<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StorageUploadsLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:link-uploads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $target = storage_path('app/uploads');
        $link = public_path('uploads');
        $result = symlink($target, $link);

        if ($result) {
            $this->info('The [uploads] directory has been linked.');
        } else {
            $this->error('Failed to create symbolic link.');
        }

        return $result ? 0 : -1;
    }
}
