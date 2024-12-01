<?php
// 设置API路径
define('API_URI', api_uri());
// 设置中文歌词
define('TLYRIC', true);
// 设置歌单文件缓存及时间
define('CACHE', false);
define('CACHE_TIME', 86400);
// 设置短期缓存-需要安装apcu
define('APCU_CACHE', false);
// 设置AUTH密钥-更改'meting-secret'
define('AUTH', false);
define('AUTH_SECRET', 'meting-secret');

if (!isset($_GET['type']) || !isset($_GET['id'])) {
    include __DIR__ . '/public/index.php';
    exit;
}

$server = isset($_GET['server']) ? $_GET['server'] : 'netease';
$type = $_GET['type'];
$id = $_GET['id'];

if (AUTH) {
    $auth = isset($_GET['auth']) ? $_GET['auth'] : '';
    if (in_array($type, ['url', 'pic', 'lrc'])) {
        if ($auth == '' || $auth != auth($server . $type . $id)) {
            http_response_code(403);
            exit;
        }
    }
}

// 数据格式
if (in_array($type, ['song', 'playlist'])) {
    header('content-type: application/json; charset=utf-8;');
} else if (in_array($type, ['name', 'lrc', 'artist'])) {
    header('content-type: text/plain; charset=utf-8;');
}

// 允许跨站
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// include __DIR__ . '/vendor/autoload.php';
// you can use 'Meting.php' instead of 'autoload.php'
include __DIR__ . '/src/Meting.php';

use Metowolf\Meting;

$api = new Meting($server);
$api->format(true);

// 设置cookie
/*if ($server == 'netease') {
    $api->cookie('os=pc; osver=Microsoft-Windows-10-Professional-build-10586-64bit; appver=2.0.3.131777; channel=netease; MUSIC_U=NMTID=00OOBwdvT0g76pOlEIJuvjUE9IW1rcAAAGTd3c4tQ; _iuqxldmzr_=32; _ntes_nnid=502625faa8e9db97fa848c57221fd9bc,1732876120390; _ntes_nuid=502625faa8e9db97fa848c57221fd9bc; WEVNSM=1.0.0; WNMCID=nmecax.1732876121379.01.0; __snaker__id=wMxzGfWkgF9dO1iG; ntes_utid=tid._.6egzyBTAcMhEFgFRQQaDDn4%252Ba28yDbWj._.0; sDeviceId=YD-jNgL7SPPxWRBE0VEVUeCWnt%2FPzsnCaH8; WM_TID=ojmC9tbDScdFUUQRBFOHX397a3oyWggE; MUSIC_U=002E11D7CF80ED048D2AFEB9B63BC45E60BF68AF19192294A772505D04575C24C680C456821CF7773ADE2BC9558AA33FB0BA4837DE90877E2EA6A4479B8027A5C594C64A5FA52FC240A6C8CCE78022A65317D9D951BAF4FD56DA29CE763648D94525B9F0F8D3C59C70155D8FD0D9FABD61B3C624700E2BFC10558B94B3900AA327C2CA7FD28BC89CAA8387D7036CBBF8B099C522797D264312B705A581B859938A082E84320C5C09CF881C01F3A88FC804D0E3914D8C09C88BCB9D833EA7580F4CB38B664EA3A080DF55E5D62FD702D7D9F5B4C3F9F7A49159BE9CF3B143080753ECD9EE255862098D2ADC4BF667EB6FC2EB9B0DA47865F447D1D5963FFFAFAC0EC00561E2360CA5E5F0157F80BC98C8ED61543A02876898DD49FFE10BAFE5D7EB26431A62B113227CB5D52C1B1593E1E81A901377822045875AFFE192A0C3519A1214E0D68FAE3802B5A033FEDF35F82B36B8179D187F6F3E3E106A6EA2C4DA044CEB8437B6B456B7018CD380A0B03EEB; __remember_me=true; __csrf=050c3f68c96b9aa641b8edc3b000fb90; ntes_kaola_ad=1; gdxidpyhxdE=I12SVpg0RHntcbOLY7H7%2BkvEN2bfE0XaRKHTsAp4MBZ63S87CdQea6HhL67yxOU8ahm%5CqyoenWkh2jXR%2BMHhODJw1U96U%2B7O2e3Jr3nHqX%5CNgQp9wDPBBQg9xqGuC5P2emnPZTlw9h8891iXRHUzdgVLoofSty8wdRbrT8HmWX2X5ugU%3A1732877867047; JSESSIONID-WYYY=SZA7Tb9xSn4HqjXk%2B8NFaYbsfnE0Siafw9PhyKSBf2etriPNG7oaS02OfBmY4nbtWAG%2FwwBx2MpVjQ100bB8%5C09x1%2FvzRfSo5NoN6RXwNG1089qySI0H%5Cgpsg%2FfarO3y6It19YT165Eo0sq1JXtlb3tB7RCmUF8pgplo9K7FF1lTOxav%3A1733046130486; WM_NI=YVD4f6famVJXu1FXWTuxwsoHwUoUIQ6aU7sTzPa1RCj9tpVFKa22F9wjgXKfpgHPm4mbDoJ9q%2BYc40oPeHp75QQzfgoAiJ5qlrAdySTKE8v%2BnY8LJ7Q52oBY7cqxrlkuT1I%3D; WM_NIKE=9ca17ae2e6ffcda170e2e6ee8bca21a1eaa4b1d37f858a8ab3c14f969f9e83c650b89f99acf16fe99c0091b32af0fea7c3b92a9b8a8cd5e53ea6bfa3b7c954b7ef8ab9d660b7a8b994ee44b58ba1b6cc5bf6bf9bd8f260ba90afd2d149aae7bc91d54e89bffed3c533b5b6a9b9ca6eb4b988b1f040afbe8c90c933b3f59784b73cad90aba9f33df78f9f9be56dad9fa7abb87bb0ae8f85fc5998958bd9f521ae87f8adc93d93ebfe90c153f5bdbf99f168a9ab9cd2cc37e2a3 ; __remember_me=true');
}*/

if ($type == 'playlist') {

    if (CACHE) {
        $file_path = __DIR__ . '/cache/playlist/' . $server . '_' . $id . '.json';
        if (file_exists($file_path)) {
            if ($_SERVER['REQUEST_TIME'] - filemtime($file_path) < CACHE_TIME) {
                echo file_get_contents($file_path);
                exit;
            }
        }
    }

    $data = $api->playlist($id);
    if ($data == '[]') {
        echo '{"error":"unknown playlist id"}';
        exit;
    }
    $data = json_decode($data);
    $playlist = array();
    foreach ($data as $song) {
        $playlist[] = array(
            'name'   => $song->name,
            'artist' => implode('/', $song->artist),
            'url'    => API_URI . '?server=' . $song->source . '&type=url&id=' . $song->url_id . (AUTH ? '&auth=' . auth($song->source . 'url' . $song->url_id) : ''),
            'pic'    => API_URI . '?server=' . $song->source . '&type=pic&id=' . $song->pic_id . (AUTH ? '&auth=' . auth($song->source . 'pic' . $song->pic_id) : ''),
            'lrc'    => API_URI . '?server=' . $song->source . '&type=lrc&id=' . $song->lyric_id . (AUTH ? '&auth=' . auth($song->source . 'lrc' . $song->lyric_id) : '')
        );
    }
    $playlist = json_encode($playlist);

    if (CACHE) {
        // ! mkdir /cache/playlist
        file_put_contents($file_path, $playlist);
    }

    echo $playlist;
} else {
    $need_song = !in_array($type, ['url', 'pic', 'lrc']);
    if ($need_song && !in_array($type, ['name', 'artist', 'song'])) {
        echo '{"error":"unknown type"}';
        exit;
    }

    if (APCU_CACHE) {
        $apcu_time = $type == 'url' ? 600 : 36000;
        $apcu_type_key = $server . $type . $id;
        if (apcu_exists($apcu_type_key)) {
            $data = apcu_fetch($apcu_type_key);
            return_data($type, $data);
        }
        if ($need_song) {
            $apcu_song_id_key = $server . 'song_id' . $id;
            if (apcu_exists($apcu_song_id_key)) {
                $song = apcu_fetch($apcu_song_id_key);
            }
        }
    }

    if (!$need_song) {
        $data = song2data($api, null, $type, $id);
    } else {
        if (!isset($song)) $song = $api->song($id);
        if ($song == '[]') {
            echo '{"error":"unknown song"}';
            exit;
        }
        if (APCU_CACHE) {
            apcu_store($apcu_song_id_key, $song, $apcu_time);
        }
        $data = song2data($api, json_decode($song)[0], $type, $id);
    }

    if (APCU_CACHE) {
        apcu_store($apcu_type_key, $data, $apcu_time);
    }

    return_data($type, $data);
}

function api_uri() // static
{
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'], '?');
}

function auth($name)
{
    return hash_hmac('sha1', $name, AUTH_SECRET);
}

function song2data($api, $song, $type, $id)
{
    $data = '';
    switch ($type) {
        case 'name':
            $data = $song->name;
            break;

        case 'artist':
            $data = implode('/', $song->artist);
            break;

        case 'url':
            $m_url = json_decode($api->url($id, 320))->url;
            if ($m_url == '') break;
            // url format
            if ($api->server == 'netease') {
                if ($m_url[4] != 's') $m_url = str_replace('http', 'https', $m_url);
            }

            $data = $m_url;
            break;

        case 'pic':
            $data = json_decode($api->pic($id, 90))->url;
            break;

        case 'lrc':
            $lrc_data = json_decode($api->lyric($id));
            if ($lrc_data->lyric == '') {
                $lrc = '[00:00.00]这似乎是一首纯音乐呢，请尽情欣赏它吧！';
            } else if ($lrc_data->tlyric == '') {
                $lrc = $lrc_data->lyric;
            } else if (TLYRIC) { // lyric_cn
                $lrc_arr = explode("\n", $lrc_data->lyric);
                $lrc_cn_arr = explode("\n", $lrc_data->tlyric);
                $lrc_cn_map = array();
                foreach ($lrc_cn_arr as $i => $v) {
                    if ($v == '') continue;
                    $line = explode(']', $v, 2);
                    // 格式化处理
                    $line[1] = trim(preg_replace('/\s\s+/', ' ', $line[1]));
                    $lrc_cn_map[$line[0]] = $line[1];
                    unset($lrc_cn_arr[$i]);
                }
                foreach ($lrc_arr as $i => $v) {
                    if ($v == '') continue;
                    $key = explode(']', $v, 2)[0];
                    if (!empty($lrc_cn_map[$key]) && $lrc_cn_map[$key] != '//') {
                        $lrc_arr[$i] .= ' (' . $lrc_cn_map[$key] . ')';
                        unset($lrc_cn_map[$key]);
                    }
                }
                $lrc = implode("\n", $lrc_arr);
            } else {
                $lrc = $lrc_data->lyric;
            }
            $data = $lrc;
            break;

        case 'song':
            $data = json_encode(array(array(
                'name'   => $song->name,
                'artist' => implode('/', $song->artist),
                'url'    => API_URI . '?server=' . $song->source . '&type=url&id=' . $song->url_id . (AUTH ? '&auth=' . auth($song->source . 'url' . $song->url_id) : ''),
                'pic'    => API_URI . '?server=' . $song->source . '&type=pic&id=' . $song->pic_id . (AUTH ? '&auth=' . auth($song->source . 'pic' . $song->pic_id) : ''),
                'lrc'    => API_URI . '?server=' . $song->source . '&type=lrc&id=' . $song->lyric_id . (AUTH ? '&auth=' . auth($song->source . 'lrc' . $song->lyric_id) : '')
            )));
            break;
    }
    if ($data == '') exit;
    return $data;
}

function return_data($type, $data)
{
    if (in_array($type, ['url', 'pic'])) {
        header('Location: ' . $data);
    } else {
        echo $data;
    }
    exit;
}
