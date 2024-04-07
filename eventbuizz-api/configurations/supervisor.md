Configuring Supervisor

Supervisor configuration files are typically stored in the /etc/supervisor/conf.d directory. Within this directory, you may create any number of configuration files that instruct supervisor how your processes should be monitored. For example, let's create a laravel-worker.conf file that starts and monitors queue:work processes:

[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/forge/app.com/artisan queue:work sqs --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=forge
numprocs=8
redirect_stderr=true
stdout_logfile=/home/forge/app.com/worker.log
stopwaitsecs=3600

In this example, the numprocs directive will instruct Supervisor to run eight queue:work processes and monitor all of them, automatically restarting them if they fail. You should change the command directive of the configuration to reflect your desired queue connection and worker options.

Note : You should ensure that the value of stopwaitsecs is greater than the number of seconds consumed by your longest running job. Otherwise, Supervisor may kill the job before it is finished processing.

Starting Supervisor
Once the configuration file has been created, you may update the Supervisor configuration and start the processes using the following commands:

Install supervisor
sudo apt-get install -y supervisor

sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*

To check if the process is running:
sudo systemctl status supervisor

To stop the process:
sudo systemctl stop supervisor

To start the process:
sudo systemctl start supervisor

List process 
supervisorctl

stop all process
supervisorctl stop all

start all process
supervisorctl start all

Dealing With Failed Jobs

Sometimes your queued jobs will fail. Don't worry, things don't always go as planned! Laravel includes a convenient way to specify the maximum number of times a job should be attempted. After an asynchronous job has exceeded this number of attempts, it will be inserted into the failed_jobs database table. Synchronously dispatched jobs that fail are not stored in this table and their exceptions are immediately handled by the application.

A migration to create the failed_jobs table is typically already present in new Laravel applications. However, if your application does not contain a migration for this table, you may use the queue:failed-table command to create the migration:

php artisan queue:failed-table
 
php artisan migrate

When running a queue worker process, you may specify the maximum number of times a job should be attempted using the --tries switch on the queue:work command. If you do not specify a value for the --tries option, jobs will only be attempted once or as many times as specified by the job class' $tries property:

php artisan queue:work redis --tries=3

Using the --backoff option, you may specify how many seconds Laravel should wait before retrying a job that has encountered an exception. By default, a job is immediately released back onto the queue so that it may be attempted again:

php artisan queue:work redis --tries=3 --backoff=3
