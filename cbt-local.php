<?php

namespace CBT;

class LocalConnection{
    
    public function __construct($username, $authkey, $proxy_ip = null, $proxy_port = null){
        $this->tunnel_proc = null;
        $this->username    = $username;
        $this->authkey     = $authkey;
        $this->proxy_ip    = $proxy_ip;
        $this->proxy_port  = $proxy_port;
    }

    public function start(){
        $start_tunnel_cmd = "&>/dev/null cbt_tunnels --username $this->username --authkey $this->authkey --ready tunnel_ready --kill tunnel_kill";
        if($this->proxy_ip && $this->proxy_port){
            $start_tunnel_cmd = $start_tunnel_cmd . " --proxyIp $this->proxy_ip --proxyPort $this->proxy_port ";
        }
        $this->tunnel_proc = popen($start_tunnel_cmd, 'r');
        if($this->is_ready()){
            print "Tunnel conencted! \n";
            return True;
        }
        else{
            die("Tunnel could not connect. Make sure it is installed correctly and your credentials are correct.");
        }
    }

    public function stop(){
        if($this->tunnel_proc){
            fclose(fopen('tunnel_kill', 'w'));
            sleep(2);
            return True;
        }
        else{
            die("Can't stop nonexistent tunnel");
        }
    }

    private function is_process_running($proc){
        if(proc_get_status($proc)['running']){
            return true;
        }
        else{
            return false;
        }
    }

    private function is_ready(){
        $failsafe_counter = 0;
        while(!file_exists("tunnel_ready") && $failsafe_counter < 20) {
            usleep(500000);
            $failsafe_counter++;
        };
        if(file_exists("tunnel_ready")){
            return True;
        }
        else{
            return False;
        }
    }
}