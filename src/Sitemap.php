<?php


namespace luo\sitemap;


class Sitemap
{
    private  $items = [];
    private  $config = [
        'title'=>'luo\sitemap\Sitemap',
        'path'=>null,
//        ''
    ];

    /**
     * 设置文件创建路径
     * @param $filename
     */
    public function setPath($filename){
        $end_str = substr($filename,-1);
        if($end_str != '/' && $end_str!= "\/") $filename .= '/';
        $this->config['path']=$filename;
    }
    /**
     * 添加一个节点
     * @param string $url
     * @param string $title html
     * @param float $priority between 1~0.5
     * @param string $changefreq  Always 经常,hourly 每小时,daily 每天,weekly 每周,monthly 每月,yearly 每年,never 从不
     */
    public function addItem($url,$title='',$priority=1,$changefreq = 'Always'):void
    {
        $lastmod = date('Y-m-d');

        $this->items[]=[
            'url'=>$url,
            'title'=>$title,
            'priority'=>$priority,
            'changefreq'=>$changefreq,
            'lastmod'=>$lastmod,
        ];
    }

    /**
     * 生成文件
     * @param string $type  xml html txt
     * @param int $chunk
     */
    public function generated(string $type,$chunk=null){
        if(!$this->items){
            die( '请添加数据->addItem');
        }elseif(!$this->config['path']){
            die( '请设置文件存放路径->setPath');
        }
        $chunk = $chunk??count($this->items);
        $items = array_chunk($this->items, $chunk);
        $function_type = 'handle'.ucfirst($type);
        foreach ($items as $k => $item) {
            $data =  $this->$function_type($item);
            $name = 'sitemap';
            if ($k) $name .= $k;
            $name .= '.'.$type;
            $this->saveFile($name,$data);
        }
    }

    private function handleHtml($arr){
        $html =  '<html style="font-size: 37.5px;"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">';
        $html .= '<meta name="robots" content="index,follow"><title>' . $this->config['title'] . ' 网站地图</title>';
        $html .= '<style type="text/css">body{font-size:14px;margin:0 auto;max-width: 640px;width:100%;border-left: 1px solid #e8e8e8;border-right: 1px solid #e8e8e8;}';
        $html .= 'h1{color:#0099CC;font-size:15px;font-weight: bold;}';
        $html .= 'a{text-decoration: none;padding:10px;overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp: 1;-webkit-box-orient: vertical;color: #393e49;white-space: nowrap;}';
        $html .= '</style></head><body>';

        foreach ($arr as $item){
            $html .= '<a href="' . $item['url'] . '">' . $item['title'] . '</a>';
        }
        $html .= '</body></html>';
        return $html;
    }
    private function handleTxt($arr){
        $txt = '';
        foreach ($arr as $item){
            $txt .= $item['url'] . "\r\n";
        }
        return $txt;
    }
    private function handleXml($arr){
        $xml = '<?xml version="1.0" encoding="utf-8"?>';
/*        $xml .= '<?xml-stylesheet type="text/xsl" href="/sitemap.xsl"?>';*/
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:mobile="http://www.baidu.com/schemas/sitemap-mobile/1/">';

        foreach ($arr as $item){
            $xml .= '<url><loc>' . $item['url'] . '</loc><changefreq>' . $item['priority'] . '</changefreq><priority>' . $item['priority'] . '</priority><lastmod>' . $item['lastmod'] . '</lastmod></url>';
        }
        $xml .= '</urlset>';
        return $xml;
    }

    /**
     * @param $file_name
     * @param $data
     */
    private function saveFile($file_name,$data){
        $filename=$this->config['path'].$file_name;
        $handle = fopen($filename, 'w+');
        !$handle && die("文件打开失败");
        flock($handle, LOCK_EX);
        if(!empty($data))
            fwrite($handle, $data);
        flock($handle, LOCK_UN);
        fclose($handle);
        0 && @chmod($filename, 0777);
    }

}