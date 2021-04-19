<?php

namespace App\Helpers;

class Tubeword {

    protected $pagination_size;

    public function __construct() {
        $this->pagination_size    = 100;
    }

    public function get_youtube_average_search_query($term) {
        return [
            "size"      => 0,
            "query"     => [
                "match"     => [
                    "term"  => $term
                ]
            ],
            "aggs"      => [
                "keyword-average-searches"  =>[
                    "composite"     => [
                        "size"      => $this->pagination_size,
                        "sources"   => [
                            [
                                "keyword"   => [
                                    "terms" => [
                                        "field"     => "term.keyword"
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "aggs"  => [
                        "most-recent-13-months" => [
                            "filter"            => [
                                "range"         => [
                                    "date"      => [
                                        "gte"   => "now-13M/M",
                                        "lte"   => "now"
                                    ]
                                ]
                            ],
                            "aggs"  => [
                                "average-searches"  => [
                                    "avg"           => [
                                        "field"     => "searches",
                                        "missing"   => 0
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function get_youtube_average_search_after_key_query($term, $after_key) {
        return [
            "size"      => 0,
            "query"     => [
                "match"     => [
                    "term"  => $term
                ]
            ],
            "aggs"      => [
                "keyword-average-searches"  =>[
                    "composite"     => [
                        "size"      => $this->pagination_size,
                        "sources"   => [
                            [
                                "keyword"   => [
                                    "terms" => [
                                        "field"     => "term.keyword"
                                    ]
                                ]
                            ]
                        ],
                        "after"     => [
                            "keyword"       => $after_key["keyword"]
                        ]
                    ],
                    "aggs"  => [
                        "most-recent-13-months" => [
                            "filter"            => [
                                "range"         => [
                                    "date"      => [
                                        "gte"   => "now-13M/M",
                                        "lte"   => "now"
                                    ]
                                ]
                            ],
                            "aggs"  => [
                                "average-searches"  => [
                                    "avg"           => [
                                        "field"     => "searches",
                                        "missing"   => 0
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function get_google_average_search_query($term) {
        return [
            "size"      => 0,
            "query"     => [
                "match"     => [
                    "term"  => $term
                ]
            ],
            "aggs"      => [
                "keyword-average-searches"      => [
                    "composite"     => [
                        "size"      => $this->pagination_size,
                        "sources"   => [
                            [
                                "keyword"       => [
                                    "terms"     => [
                                        "field" => "term.keyword"
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "aggs"          => [
                        "most-recent-13-months"     => [
                            "filter"            => [
                                "range"         => [
                                    "date"      => [
                                        "gte"   => "now-13M/M",
                                        "lte"   => "now"
                                    ]
                                ]
                            ],
                            "aggs"  => [
                                "average-searches"  => [
                                    "avg"           => [
                                        "field"     => "searches",
                                        "missing"   => 0
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                "keyword-competition-data"      => [
                    "composite"     => [
                        "size"      => $this->pagination_size,
                        "sources"   => [
                            [
                                "keyword"       => [
                                    "terms"     => [
                                        "field" => "term.keyword"
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "aggs"          => [
                        "most-recent-data"      => [
                            "top_hits"          => [
                                "size"          => 1
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function get_google_average_search_after_key_query($term, $after_key) {
        return [
            "size"      => 0,
            "query"     => [
                "match"     => [
                    "term"  => $term
                ]
            ],
            "aggs"      => [
                "keyword-average-searches"      => [
                    "composite"     => [
                        "size"      => $this->pagination_size,
                        "sources"   => [
                            [
                                "keyword"       => [
                                    "terms"     => [
                                        "field" => "term.keyword"
                                    ]
                                ]
                            ]
                        ],
                        "after"     => [
                            "keyword"           => $after_key["keyword"]
                        ]
                    ],
                    "aggs"          => [
                        "most-recent-13-months"     => [
                            "filter"            => [
                                "range"         => [
                                    "date"      => [
                                        "gte"   => "now-13M/M",
                                        "lte"   => "now"
                                    ]
                                ]
                            ],
                            "aggs"  => [
                                "average-searches"  => [
                                    "avg"           => [
                                        "field"     => "searches",
                                        "missing"   => 0
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                "keyword-competition-data"      => [
                    "composite"     => [
                        "size"      => $this->pagination_size,
                        "sources"   => [
                            [
                                "keyword"       => [
                                    "terms"     => [
                                        "field" => "term.keyword"
                                    ]
                                ]
                            ]
                        ],
                        "after"     => [
                            "keyword"           => $after_key["keyword"]
                        ]
                    ],
                    "aggs"          => [
                        "most-recent-data"      => [
                            "top_hits"          => [
                                "size"          => 1
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function build_related_searches_data($youtube_data, $google_average_searches, $google_competition_data) {
        $related_searches_data  = array();

        $related_searches_data  = $this->related_searches_insert_youtube_data($youtube_data);
        $related_searches_data  = $this->related_searches_insert_google_data($related_searches_data, $google_average_searches, $google_competition_data);

        return $related_searches_data;
    }

    private function related_searches_insert_youtube_data($youtube_data) {
        $new_data_set   = array();

        foreach ($youtube_data as $data) {
            array_push($new_data_set, [
                "keyword"                   => $data["key"]["keyword"],
                "youtube-average-searches"  => $data["most-recent-13-months"]["average-searches"]["value"],
                "google-average-searches"   => 0,
                "competition-count"         => 0
            ]);
        }

        return $new_data_set;
    }

    private function related_searches_insert_google_data($related_searches_data, $google_average_searches, $google_competition_data) {
        $new_data_set   = $related_searches_data;

        foreach ($google_average_searches as $data) {
            $index      = $this->get_array_index($new_data_set, $data["key"]["keyword"]);
            if ($index >= 0) {
                $new_data_set[$index]["google-average-searches"]    = $data["most-recent-13-months"]["average-searches"]["value"];
                $new_data_set[$index]["competition-count"]          = 0;
            } else {
                array_push($new_data_set, [
                    "keyword"                   => $data["key"]["keyword"],
                    "youtube-average-searches"  => 0,
                    "google-average-searches"   => $data["most-recent-13-months"]["average-searches"]["value"],
                    "competition-count"         => 0
                ]);
            }
        }

        foreach ($google_competition_data as $data) {
            $index      = $this->get_array_index($new_data_set, $data["key"]["keyword"]);
            if ($index >= 0) {
                $new_data_set[$index]["competition-count"]          = $data["most-recent-data"]["hits"]["hits"][0]["_source"]["cpc"];
            } else {
                array_push($new_data_set, [
                    "keyword"                   => $data["key"]["keyword"],
                    "youtube-average-searches"  => 0,
                    "google-average-searches"   => 0,
                    "competition-count"         => $data["most-recent-data"]["hits"]["hits"][0]["_source"]["cpc"]
                ]);
            }
        }

        return $new_data_set;
    }

    private function get_array_index($array, $keyword) {
        foreach ($array as $i => $item) {
            if ($item["keyword"] == $keyword)
                return $i;
        }

        return -1;
    }

}
