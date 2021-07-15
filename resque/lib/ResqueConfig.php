<?php
/**
 * @author      Laurent Jouanneau
 *
 * @copyright   2021 Laurent Jouanneau
 *
 * @see        http://www.jelix.org
 * @licence     MIT
 */
namespace Jelix\Resque;


class ResqueConfig {

    protected $profile = array();

    protected $dsn = '';

    /**
     *
     * @param  array  $profile content of a profile to connect to redis and to
     *                          initialize the server
     *
     * @throws \Exception
     */
    public function __construct(array $profile)
    {
        $profile = array_merge(
            array(
                'redis_host' => '127.0.0.1',
                'redis_port' => 6379,
                'redis_password' => '',
                'redis_user' => '',
                'redis_db' => 0,
                'redis_prefix' => '',
                'worker_count' => 1,
                'queue' => \Jelix\Core\Infos\AppInfos::load()->name,
                'logging' => false,
                'verbose' => false,
                'blocking' => false,
                'interval' => 5,
            ),
            $profile
        );
        $this->profile = $profile;

        $dsn = $profile['redis_host'];
        if ($profile['redis_port'] !== '') {
            $dsn .= ':'.$profile['redis_port'];
        }

        if ($profile['redis_password'] != '') {
            $dsn = $profile['redis_user'].':'.$profile['redis_password'].'@'.$dsn;
        }

        $this->dsn = 'redis://'.$dsn.'/'.$profile['redis_db'];
    }

    /**
     * Returns a profile to be used with redis API
     * @return array
     */
    public function getRedisProfile()
    {
        $profile = $this->profile;

        $redis = array('host' => '127.0.0.1',
                       'port' => 6379,
                       'password'=>'',
                       'user'=>'',
                       'db'=>0,
                       'timeout'=> null
        );
        foreach($redis as $option => $default) {
            if (isset($profile['redis_'.$option])) {
                $redis[$option] = $profile['redis_'.$option];
            }
        }
        return $redis;
    }

    /**
     * initialize environment variables for the Resque server
     */
    public function prepareEnvironment()
    {

        $profile = $this->profile;

        putenv('REDIS_BACKEND='.$this->dsn);

        if ($profile['redis_prefix'] != '') {
            putenv('PREFIX='.$profile['prefix']);
        }
        if ($profile['worker_count']) {
            putenv('COUNT='.$profile['worker_count']);
        }
        putenv("QUEUE=".$profile['queue']); // list of queues

        putenv("LOGGING=".($profile['logging']?1:0));
        putenv("VERBOSE=".($profile['verbose']?1:0));
        putenv("VVERBOSE=0");
        putenv("BLOCKING=".($profile['blocking']?1:0));
        putenv("INTERVAL=".$profile['interval']);
    }

}
