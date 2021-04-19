<?php

namespace App\Elasticsearch;

class RelatedSearchesData {

    public $keyword;
    public $youtube_average_searches;
    public $google_average_searches;
    public $competition_count;

    public function __construct($data) {
        $this->keyword                      = $data["keyword"];
        $this->youtube_average_searches     = $data["youtube-average-searches"];
        $this->google_average_searches      = $data["google-average-searches"];
        $this->competition_count            = $data["competition-count"];
    }

}
