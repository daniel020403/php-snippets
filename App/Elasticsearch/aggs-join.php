<?php

/**
 * Usage:
 * - php aggs-join.php "<term>"
 */

namespace App\Elasticsearch;

use App\Helpers\Elasticsearch;
use App\Helpers\Tubeword;
use App\Elasticsearch\RelatedSearches;

$root_dir   = realpath(__DIR__ . "/../../");
require_once $root_dir . "/autoload.php";

$elasticsearch_configs  = [
    "host"      => "https://localhost:9300",
    "indices"   => [
        "youtube"   => "tubeword-youtube",
        "google"    => "tubeword-google"
    ]
];

$term               = $argv[1];
$es_helper          = new Elasticsearch($elasticsearch_configs["host"]);
$tubeword_helper    = new Tubeword();
$display_count      = 10;

$related_searches       = new RelatedSearches($term, $es_helper, $tubeword_helper, $elasticsearch_configs);
$related_searches->build();
$related_searches_data  = $related_searches->get_related_searches_data();
// echo $related_searches_data;
var_dump(count($related_searches_data));
$count = 0;
foreach ($related_searches_data as $data) {
    if ($count >= $display_count) break;
    // echo json_encode($data) . "\n";
    // var_dump($data);
    echo $data->keyword . " |  " .  $data->youtube_average_searches . " |  " . $data->google_average_searches . " |  " . $data->competition_count . "\n";
    $count++;
}

/**
 * ASC order by keyword
 */
echo "-------------------------";
echo "\nASC order by keyword:\n";
echo "-------------------------";
$related_searches->sort("keyword", "ASC");
$related_searches_data  = $related_searches->get_related_searches_data();
var_dump(count($related_searches_data));
$count = 0;
foreach ($related_searches_data as $data) {
    if ($count >= $display_count) break;
    // echo json_encode($data) . "\n";
    // var_dump($data);
    echo $data->keyword . " |  " .  $data->youtube_average_searches . " |  " . $data->google_average_searches . " |  " . $data->competition_count . "\n";
    $count++;
}

/**
 * DESC order by keyword
 */
echo "-------------------------";
echo "\nDESC order by keyword:\n";
echo "-------------------------";
$related_searches->sort("keyword", "DESC");
$related_searches_data  = $related_searches->get_related_searches_data();
var_dump(count($related_searches_data));
$count = 0;
foreach ($related_searches_data as $data) {
    if ($count >= $display_count) break;
    // echo json_encode($data) . "\n";
    // var_dump($data);
    echo $data->keyword . " |  " .  $data->youtube_average_searches . " |  " . $data->google_average_searches . " |  " . $data->competition_count . "\n";
    $count++;
}

/**
 * ASC order by youtube average searches
 */
echo "-------------------------";
echo "\nASC order by youtube_average_searches:\n";
echo "-------------------------";
$related_searches->sort("youtube_average_searches", "ASC");
$related_searches_data  = $related_searches->get_related_searches_data();
var_dump(count($related_searches_data));
$count = 0;
foreach ($related_searches_data as $data) {
    if ($count >= $display_count) break;
    // echo json_encode($data) . "\n";
    // var_dump($data);
    echo $data->keyword . " |  " .  $data->youtube_average_searches . " |  " . $data->google_average_searches . " |  " . $data->competition_count . "\n";
    $count++;
}

/**
 * DESC order by youtube average searches
 */
echo "-------------------------";
echo "\nDESC order by youtube_average_searches:\n";
echo "-------------------------";
$related_searches->sort("youtube_average_searches", "DESC");
$related_searches_data  = $related_searches->get_related_searches_data();
var_dump(count($related_searches_data));
$count = 0;
foreach ($related_searches_data as $data) {
    if ($count >= $display_count) break;
    // echo json_encode($data) . "\n";
    // var_dump($data);
    echo $data->keyword . " |  " .  $data->youtube_average_searches . " |  " . $data->google_average_searches . " |  " . $data->competition_count . "\n";
    $count++;
}

/**
 * filter by youtube average searches
 */
echo "-------------------------";
echo "\nfilter by youtube_average_searches:\n";
echo "-------------------------";
$related_searches->filter_average_searches("youtube", 1000, 2000);
$related_searches_data  = $related_searches->get_related_searches_data();
var_dump(count($related_searches_data));
$count = 0;
foreach ($related_searches_data as $data) {
    if ($count >= $display_count) break;
    // echo json_encode($data) . "\n";
    // var_dump($data);
    echo $data->keyword . " |  " .  $data->youtube_average_searches . " |  " . $data->google_average_searches . " |  " . $data->competition_count . "\n";
    $count++;
}
