<?php

declare(strict_types=1);

namespace CodelDev\LaravelJobLog\Tests;

use CodelDev\LaravelJobLog\LaravelJobLogServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected static Migration $migration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createFailedJobsTable();
        $this->prepareMigration();

        self::$migration->up();

        $this->setFactoriesPath();
    }

    /** @param Application $app */
    public function getEnvironmentSetUp($app): void
    {
        Model::preventLazyLoading();

        $app['config']->set('database.default', 'testing');
        $app['config']->set('job-log', require __DIR__ . '/../config/job-log.php');
    }

    /** @param Application $app */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelJobLogServiceProvider::class,
        ];
    }

    private function prepareMigration(): void
    {
        self::$migration = include __DIR__ . '/../database/migrations/create_job_log_table.php.stub';
    }

    private function createFailedJobsTable(): void
    {
        if (Schema::hasTable('failed_jobs'))
        {
            return;
        }

        Schema::create('failed_jobs', static function (Blueprint $table): void
        {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    private function setFactoriesPath(): void
    {
        Factory::guessFactoryNamesUsing(
            static fn (string $modelName) => 'CodelDev\\LaravelJobLog\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }
}
