<?php

use EasyCorp\Bundle\EasyDeployBundle\Deployer\DefaultDeployer;

return new class extends DefaultDeployer
{
    public function configure()
    {
        $deploymentUser=  $_ENV["DEPLOYMENT_USER"];
        $deploymentHost=  $_ENV["DEPLOYMENT_HOST"];
        return $this->getConfigBuilder()
            // SSH connection string to connect to the remote server (format: user@host-or-IP:port-number)
            ->server(sprintf('%s@%s', $deploymentUser, $deploymentHost))
            // the absolute path of the remote server directory where the project is deployed
            ->deployDir('/var/www/api-particulier-portail')
            // the URL of the Git repository where the project code is hosted
            ->repositoryUrl('git@github.com:betagouv/api-particulier-portail.git')
            // the repository branch to deploy
            ->repositoryBranch('master')
            ->sharedFilesAndDirs([
                '.env'
            ])
        ;
    }

    // run some local or remote commands before the deployment is started
    public function beforeStartingDeploy()
    {
        // $this->runLocal('./vendor/bin/simple-phpunit');
    }

    // run some local or remote commands after the deployment is finished
    public function beforeFinishingDeploy()
    {
        // $this->runRemote('{{ console_bin }} app:my-task-name');
    }
};
