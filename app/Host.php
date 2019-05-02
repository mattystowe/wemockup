<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Log;
use Aws\Sdk;

class Host extends Model
{

    //
    //
    //
    //
    //
    //
    public function getHostname() {
      if (env('APP_ENV') == 'local' or env('APP_ENV') == 'testing') {
        return 'localhost';
      } else {
        //ci or live - get instance metadata from ec2:
        $public_hostname = file_get_contents('http://169.254.169.254/latest/meta-data/public-hostname');
        return $public_hostname;
      }
    }



    public function getInstanceId() {
      if (env('APP_ENV') == 'local' or env('APP_ENV') == 'testing') {
        return 'localid';
      } else {
        //ci or live - get instance metadata from ec2:
        $instance_id = file_get_contents('http://169.254.169.254/latest/meta-data/instance-id');
        return $instance_id;
      }
    }




    public function getInstanceType() {
      if (env('APP_ENV') == 'local' or env('APP_ENV') == 'testing') {
        return 'local';
      } else {
        //ci or live - get instance metadata from ec2:
        $instance_type = file_get_contents('http://169.254.169.254/latest/meta-data/instance-type');
        return $instance_type;
      }
    }




    /**
     * Set the instance scale in protection flag for auto scaling events.
     *
     * Protects the instance from being scaled down during a scalein event and being terminated mid process.
     *
     *
     *
     *
     * @param [type] $state [description]
     */
    public function setInstanceScaleProtection($state) {
      //Log::debug('Setting Instance Protection: ' . $this->getInstanceId());


      if (env('APP_ENV') == 'local' or env('APP_ENV') == 'testing') {
        //no sclaing events to protect from on local/text environments
        Log::debug('Set Instance State to ' . $state);
      } else {

          $sharedConfig = [
              'region'  => env('AWS_REGION'),
              'version' => 'latest',
              'credentials' => array(
                  'key'    => env('AWS_KEY'),
                  'secret' => env('AWS_SECRET'),
              ),
          ];

          $instanceId = $this->getInstanceId();

          // Create an autoscaling client
          $sdk = new \Aws\Sdk($sharedConfig);
          $autoScalingClient = $sdk->createAutoScaling();

          $result = $autoScalingClient->describeAutoScalingInstances([
              'InstanceIds' => [$instanceId]
          ]);

          //Log::debug($result['AutoScalingInstances']);

          if (is_array($result['AutoScalingInstances']) && count($result['AutoScalingInstances'])>0) {
            $autoScalingGroupName = $result['AutoScalingInstances'][0]['AutoScalingGroupName'];
            //Log::debug('Group Name = ' . $autoScalingGroupName);
            if ($result['AutoScalingInstances'][0]['LifecycleState'] == 'InService' || $result['AutoScalingInstances'][0]['LifecycleState'] == 'EnteringStandby' || $result['AutoScalingInstances'][0]['LifecycleState'] == 'Standby') {
                Log::debug('Set Instance ' . $instanceId . ' (' . $autoScalingGroupName . ') protectionState to ' . $state);
                $result = $autoScalingClient->setInstanceProtection([
                    'AutoScalingGroupName' => $autoScalingGroupName,
                    'InstanceIds' => array($instanceId),
                    'ProtectedFromScaleIn' => $state
                ]);
            }

            //Log::debug($result);
          }


      }

    }






}
