<?php
/**
 * Created by PhpStorm.
 * User: Fabricio
 * Date: 28/07/2016
 * Time: 16:32
 */

namespace app\library\youtube;


class Live
{

    private $youtube;
    private $videos;

    public function __construct(\Google_Service_YouTube $youtube)
    {
        $this->youtube = $youtube;
    }

    public function findLiveVideo($chanel_id){
        //pega os dados do live video
        $this->videos = $this->youtube->search->listSearch("id,snippet", array(
            "channelId" => $chanel_id,
            "eventType" => "live",
            "type" => "video",
        ));

        return $this;
    }

    public function getEmbedVideo(){
        if(count($this->videos->items) > 0){
            $video = $this->videos->items[0];
            return $this->embedCode($video['id']['videoId']);
        }

        return '';
    }

    public function getVideos(){
        return $this->videos;
    }

    private function embedCode($video_id){
        $embed_code = '
            <iframe
                width="100%"
                height="100%"
                src="//www.youtube.com/embed/'. $video_id . '?autoplay=1"
                frameborder="0"
                allowfullscreen>
            </iframe>';

        return $embed_code;
    }

}