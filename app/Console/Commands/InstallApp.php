<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Installation\AppInstallationService;

class InstallApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Base Application';

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
     * @return mixed
     */
    public function handle()
    {
        $service = app(AppInstallationService::class);
        $this->info('Welcome to the Installer, please provide the following information');
        $name = $this->ask('What is the Admininstrator\'s name?');
        $email = $this->ask('What is the Admininstrator\'s email?');
        $password = $this->ask('What is the Admininstrator\'s password?');
        $this->info('Installing the app');
        $service->installApp([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);
        $this->info('All Done');
    }
}
