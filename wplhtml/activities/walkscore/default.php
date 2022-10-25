<?php
/** No direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** Set params **/
$this->license_key_checker = false;
$wpl_properties = isset($params['wpl_properties']) ? $params['wpl_properties'] : array();

/** skip if address is hide **/
if(!$wpl_properties['current']['data']['show_address']) return;

$this->property_address = isset($wpl_properties['current']['location_text']) ? $wpl_properties['current']['location_text'] : NULL;
$this->walkscore_googlemap_lt = isset($wpl_properties['current']['data']['googlemap_lt']) ? $wpl_properties['current']['data']['googlemap_lt'] : NULL;
$this->walkscore_googlemap_ln = isset($wpl_properties['current']['data']['googlemap_ln']) ? $wpl_properties['current']['data']['googlemap_ln'] : NULL;

/** Get Activity options */
$this->walkscore_license_key = isset($params['walkscore_license_key']) ? $params['walkscore_license_key'] : NULL;
$this->walkscore_width = isset($params['walkscore_width']) ? $params['walkscore_width'] : '317';
$this->walkscore_height = isset($params['walkscore_height']) ? $params['walkscore_height'] : '460';
$this->walkscore_layout = isset($params['walkscore_layout']) ? $params['walkscore_layout'] : 'vertical';

/** load js codes **/
$this->_wpl_import($this->tpl_path.'.scripts.default', true, true, true);

$address = $this->property_address;
$address=urlencode($address);


$url = "https://api.walkscore.com/score?format=json&address=".$this->property_address;
$url .= "&lat=".$this->walkscore_googlemap_lt."&lon=".$this->walkscore_googlemap_ln."&transit=true&bike=1&wsapikey=".$this->walkscore_license_key;
$response = wp_remote_get( esc_url_raw( $url ) );
$api_response = json_decode( wp_remote_retrieve_body( $response ), true );
//   echo "<pre>";
  //print_r($this->walkscore_license_key);
//   echo "</pre>";

//Full Descriptions

if ($api_response['walkscore'] < 25){ $ws_fdescription = "Almost all errands require a car";}
if ($api_response['transit']['score'] < 25){ $trans_fdescription = "It is possible to get on a bus";}
if ($api_response['bike']['score'] < 49){ $bike_fdescription = "Minimal bike infrastructure";}

if ($api_response['walkscore'] > 25){ $ws_fdescription = "Most errands require a car";}
if ($api_response['transit']['score'] > 25){ $trans_fdescription = "A few nearby public transportation options";}
if ($api_response['bike']['score'] > 49){ $bike_fdescription = "Some bike infrastructure";}

if ($api_response['walkscore'] > 49){ $ws_fdescription = "Some errands can be accomplished on foot";}
if ($api_response['transit']['score'] > 49){ $trans_fdescription = "Many nearby public transportation options";}
if ($api_response['bike']['score'] > 69){ $bike_fdescription = "Biking is convenient for most trips";}

if ($api_response['walkscore'] > 69){ $ws_fdescription = "Most errands can be accomplished on foot";}
if ($api_response['transit']['score'] > 69){ $trans_fdescription = "Transit is convenient for most trips";}
if ($api_response['bike']['score'] > 89){ $bike_fdescription = "Daily errands can be accomplished on a bike";}

if ($api_response['walkscore'] > 89){ $ws_fdescription = "Daily errands do not require a car";}
if ($api_response['transit']['score'] > 89){ $bike_fdescription = "World-class public transportation";}

if ($api_response['walkscore'] == NULL){ $ws_fdescription = "N/A"; }
if ($api_response['transit']['score'] == NULL){ $trans_fdescription = "N/A"; }
if ($api_response['bike']['score'] == NULL){ $bike_fdescription = "N/A"; }

?>

<style>

/*move to CSS file*/

.walkscore {}

.walkscore .inner-walkscore {}

.walkscore .inner-walkscore .row {}

.walkscore .inner-walkscore .row .heading {}

.walkscore .inner-walkscore .row .heading h1 {margin-bottom: 40px;padding-left: 50px;}

.walkscore .inner-walkscore .row .walkscoreBox {}

.walkscore .inner-walkscore .row .walkscoreBox .wsBox-top {display: flex;align-items: center;background-color: rgba(248,248,248, 1);padding: 14px 28px;}

.walkscore .inner-walkscore .row .walkscoreBox .wsBox-top .wsBox-img {width: 60px;height: 60px;margin-right: 12px;}

.walkscore .inner-walkscore .row .walkscoreBox .wsBox-top .wsBox-img img {width: 100%;height: 70%;margin-top: 7px;}

.walkscore .inner-walkscore .row .walkscoreBox .wsBox-top .wsBox-img-bike img {width: 100%;height: 70%;margin-top: 7px;}

.walkscore .inner-walkscore .row .walkscoreBox .wsBox-top .wsBox-contet {}

.walkscore .inner-walkscore .row .walkscoreBox .wsBox-top .wsBox-contet h2 {font-weight: 600;}

.walkscore .inner-walkscore .row .walkscoreBox .wsBox-top .wsBox-contet h3 {font-size: 24px;color: gray;}

.walkscore .inner-walkscore .row .walkscoreBox .wsBox-bottom {padding: 30px;}

.walkscore .inner-walkscore .row .walkscoreBox .wsBox-bottom h5 {}

.walkscore .inner-walkscore .row .walkscoreBox .wsBox-bottom h5 p {}
.ws-bold{font-weight: 700;font-family: 'Inter';font-size: 14px;}
#ws-box-bdr{border-right: 1px solid rgba(235,235,235, 1);}
.walk-main{padding: 0px;color: #191919;}
.wsBox-bottom p{font-size: 12px;}
.count-bold{font-weight: 700;font-family: 'Inter';font-size: 18px;color:#191919;}
.count-descript{font-weight: 400;font-family: 'Inter';font-size: 14px;color:#191919;line-height: 1.5;}
.wpl_prp_position3_boxes.listing_additional, .wpl_prp_position3_boxes.walkscore {
    padding: 30px 0;
}
.walkscore .wpl_prp_position3_boxes_title {
    margin-left: 30px;
    font-family: 'Inter' !important;
    font-weight: 700;
}
.walkscore .inner-walkscore .row .walkscoreBox .wsBox-bottom {
    padding: 30px 30px 1px 30px;
}
.wsBox-top.bike {
    height: 88px;
}
</style>
<section class="walkscore">
	<div class="inner-walkscore">
		<div class="container-fluid p-0">
			<div class="row">
				<!--<div class="col-12">-->
				<!--	<div class="heading">-->
				<!--		<h1>Walkscore</h1>-->
				<!--	</div>-->
				<!--</div>-->
				<div class="col-12 col-md-4 walk-main">
					<div class="walkscoreBox">
						<div class="wsBox-top" id="ws-box-bdr">
							<div class="wsBox-img">
								<img src="/wp-content/uploads/sites/49/2022/05/Walk.svg" alt="">
							</div>
							<div class="wsBox-contet">
								<span class="count-bold"><?php echo $api_response['walkscore'] ?? 'N/A' ?></span>
								<br>
								<span class="count-descript">Walk Score</span>
							</div>
						</div>
						<div class="wsBox-bottom">
							<span class="ws-bold"><?php echo $api_response['description'] ?? 'N/A' ?></span>
							<p><?php echo $ws_fdescription ?></p>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-4 walk-main">
					<div class="walkscoreBox">
						<div class="wsBox-top" id="ws-box-bdr">
							<div class="wsBox-img">
								<img src="/wp-content/uploads/sites/49/2022/05/Transit.svg" alt="">
							</div>
							<div class="wsBox-contet">
								<span class="count-bold"><?php echo $api_response['transit']['score'] ?? 'N/A' ?></span>
								<br>
								<span class="count-descript">Transit Score</span>
							</div>
						</div>
						<div class="wsBox-bottom">
							<span class="ws-bold"><?php echo $api_response['transit']['description'] ?? 'N/A' ?></span>
							<?php //print_r($api_response['description']); ?>
							<p><?php echo $trans_fdescription ?></p>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-4 walk-main">
					<div class="walkscoreBox">
						<div class="wsBox-top bike">
							<div class="wsBox-img-bike">
								<img src="/tosell/wp-content/uploads/sites/49/2022/05/Bike.svg" alt="">
							</div>
							&nbsp;&nbsp;&nbsp;
							<div class="wsBox-contet">
								<span class="count-bold"><?php echo $api_response['bike']['score'] ?? 'N/A' ?></span>
								<br>
								<span class="count-descript">Bike Score</span>
							</div>
						</div>
						<div class="wsBox-bottom">
							<span class="ws-bold"><?php echo $api_response['bike']['description'] ?? 'N/A' ?></span>
							<p><?php echo $bike_fdescription ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!--<div id="ws-walkscore-tile"></div>-->


<script>
// let elements = document.getElementsByName(ws-walkscore-tile[alt]);
// alert(elements);
</script>