<?php

namespace Deployer;

require 'recipe/symfony.php';

set('application', 'picgarden-api');

set('repository', 'git@bitbucket.org:hursit_topal/picgarden-api.git');

set('git_tty', false);

set('bin_dir', '/vendor/symfony');

host(
	'167.99.165.8'
)
    ->user('root')
    ->set('http_user', 'www-data')
    ->set('branch', 'master')
    ->set('deploy_path', '/var/www/html/picgarden-api')
;

task('pwd', function () {
    $result = run('pwd');
    writeln("Current dir: $result");
});

task('build', function () {
    run('cd {{release_path}} && build');
});

after('deploy:failed', 'deploy:unlock');
