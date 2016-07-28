<?php
/**
 * Created by PhpStorm.
 * User: Fabricio
 * Date: 28/07/2016
 * Time: 15:03
 */

namespace app\library\youtube;


class Channel
{

    private $channels;
    private $youtube;

    public function __construct(\Google_Service_YouTube $youtube)
    {
        $this->youtube = $youtube;
    }

    public function findUserChanel(){
        $channelInfo = $this->youtube->channels->listChannels("snippet,localizations", array(
            'mine' => true
        ));
        $channelInfo->setKind('youtube#channel');

        $this->channels = $channelInfo->getItems();

        return $this;
    }

    public function getChannelId(){
        if(count($this->channels) > 0){
            return $this->channels[0]->id;
        }
        return 0;
    }

}