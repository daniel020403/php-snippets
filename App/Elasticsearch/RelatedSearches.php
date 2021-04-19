<?php

namespace App\Elasticsearch;

use App\Elasticsearch\RelatedSearchesData;

class RelatedSearches {

    protected $term;
    protected $elasticsearch_helper;
    protected $tubeword_helper;
    protected $elasticsearch_configs;
    protected $data;
    protected $processed_data;

    public function __construct($term, $elasticsearch_helper, $tubeword_helper, $elasticsearch_configs)
    {
        $this->term                     = $term;
        $this->elasticsearch_helper     = $elasticsearch_helper;
        $this->tubeword_helper          = $tubeword_helper;
        $this->elasticsearch_configs    = $elasticsearch_configs;
        $this->data                     = [];
        $this->processed_data           = [];
    }

    public function get_related_searches_data() {
        return $this->processed_data;
    }

    public function build() {
        $youtube_data           = $this->retrieve_youtube_data();
        $google_data            = $this->retrieve_google_data();
        $related_searches_data  = $this->tubeword_helper->build_related_searches_data($youtube_data, $google_data["average-searches"], $google_data["competition-data"]);
        $this->data             = array_map(array($this,"data_to_related_searches_data_object"), $related_searches_data);
        $this->processed_data   = $this->data;
    }

    public function sort($key, $order = "ASC"){
        $this->processed_data   = $this->data;

        switch ($key) {
            case "keyword":
                $this->order_by_keyword($order);
                break;
            case "youtube_average_searches":
                $this->order_by_youtube_average_searches($order);
                break;
        }
    }

    public function filter_average_searches($key, $lower_limit, $upper_limit) {
        switch ($key) {
            case "youtube":
                $this->processed_data = $this->filter_youtube_average_searches($lower_limit, $upper_limit);
                break;
        }
    }

    private function data_to_related_searches_data_object($data) {
        $related_searches_data_object   = new RelatedSearchesData($data);
        return $related_searches_data_object;
    }

    private function order_by_keyword($order) {
        usort($this->processed_data, function($object1, $object2) use ($order) {
            if (strtolower($order) == "asc")
                return $object1->keyword > $object2->keyword;
            elseif (strtolower($order) == "desc")
                return $object1->keyword < $object2->keyword;
        });
    }

    private function order_by_youtube_average_searches($order) {
        usort($this->processed_data, function($object1, $object2) use ($order) {
            if (strtolower($order) == "asc")
                return $object1->youtube_average_searches > $object2->youtube_average_searches;
            elseif (strtolower($order) == "desc")
                return $object1->youtube_average_searches < $object2->youtube_average_searches;
        });
    }

    private function filter_youtube_average_searches($lower_limit, $upper_limit) {
        return array_filter($this->processed_data, function($key, $val) use ($lower_limit, $upper_limit) {
            return $key->youtube_average_searches >= $lower_limit && $key->youtube_average_searches <= $upper_limit;
        }, ARRAY_FILTER_USE_BOTH);
    }

    private function retrieve_youtube_data() {
        $youtube_data           = array();
        $search_query           = $this->tubeword_helper->get_youtube_average_search_query($this->term);

        do {
            $search_result      = json_decode($this->elasticsearch_helper->search($this->elasticsearch_configs["indices"]["youtube"], $search_query)->getBody(), true);
            $buckets            = $search_result["aggregations"]["keyword-average-searches"]["buckets"];
            $bucket_count       = count($buckets);

            if ($bucket_count > 0) {
                $youtube_data   = array_merge($youtube_data, $buckets);
                $after_key      = $search_result["aggregations"]["keyword-average-searches"]["after_key"];
                $search_query   = $this->tubeword_helper->get_youtube_average_search_after_key_query($this->term, $after_key);
            }
        } while ($bucket_count > 0);

        return $youtube_data;
    }

    private function retrieve_google_data() {
        $google_data            = [
            "average-searches"  => [],
            "competition-data"  => []
        ];
        $search_query           = $this->tubeword_helper->get_google_average_search_query($this->term);

        do {
            $search_result              = json_decode($this->elasticsearch_helper->search($this->elasticsearch_configs["indices"]["google"], $search_query)->getBody(), true);
            $average_searches_buckets   = $search_result["aggregations"]["keyword-average-searches"]["buckets"];
            $competition_data_buckets   = $search_result["aggregations"]["keyword-competition-data"]["buckets"];
            $bucket_count               = count($average_searches_buckets);

            if ($bucket_count > 0) {
                $google_data["average-searches"]    = array_merge($google_data["average-searches"], $average_searches_buckets);
                $google_data["competition-data"]    = array_merge($google_data["competition-data"], $competition_data_buckets);
                $after_key                          = $search_result["aggregations"]["keyword-average-searches"]["after_key"];
                $search_query                       = $this->tubeword_helper->get_google_average_search_after_key_query($this->term, $after_key);
            }
        } while ($bucket_count > 0);

        return $google_data;
    }

}
