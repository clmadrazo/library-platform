<?php

namespace Library\Util;

class Flickr {

    private $api_key;
    private $post;

    function __construct($post) {
        $this->post = $post;
        $this->api_key = 'e397763e78e4df24996e7ec8cbc042e6';
    }

    function request($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        return json_decode($result);
    }

    function get_photo($image_link) {
        $image = array();
        $info = $this->get_info($image_link);

        if ($info == false) {
            $image['success'] = false;
            $image['message'] = 'The image you are trying to add to your article doesn\'t seem to be valid';
            $image['errorCode'] = '600';
            return $image;
        }

        if (isset($info->photo) and $this->validate_license($info->photo->license)) {
            $image_sizes = $this->get_sizes($info->photo->id);
            $img = false;
            foreach ($image_sizes->sizes->size as $size) {
                if (($img === false) and $this->validate_size($size->width, $size->height)) {
                    $img = $size->source;
                    break;
                }
            }

            if ($img !== false) {
                if ($this->validate_duplicity($info->photo->id)) {
                    $image['bin_img'] = file_get_contents($img);
                    $image['flickr_id'] = $info->photo->id;
                    $image['license'] = $info->photo->license;
                    $image['cache'] = json_encode($info);
                    $image['success'] = true;
                } else {
                    $image['success'] = false;
                    $image['message'] = 'The image you are trying to add to your article has already been used earlier in this project. Please search for another image and try to add it again. Thank you.';
                    $image['errorCode'] = '300';
                }
            } else {
                $image['success'] = false;
                $image['message'] = 'The image you are trying to add do not have the minimun size required, Please add another image.';
                $image['errorCode'] = '400';
            }
        } else {
            $image['success'] = false;
            $image['message'] = 'The image you are trying to add to your article does not meet the accepted CC licenses in this project. Please read the guidelines carefully and look for an image that meets the project requirements. Thank you.';
            $image['errorCode'] = '500';
        }

        return $image;
    }

    function validate_size($width, $height) {
        //echo $license;
        if (($this->post->getProject()->getPostTypeSpecificInfo()->treatmentImageWidth !== null && $this->post->getProject()->getPostTypeSpecificInfo()->treatmentImageWidth > 0) || ($this->post->getProject()->getPostTypeSpecificInfo()->treatmentImageHeight !== null && $this->post->getProject()->getPostTypeSpecificInfo()->treatmentImageHeight > 0)) {
            if ((int) $this->post->getProject()->getPostTypeSpecificInfo()->treatmentImageWidth < $width and (int) $this->post->getProject()->getPostTypeSpecificInfo()->treatmentImageHeight < $height)
                return true;
            else
                return false;
        }else {
            return true;
        }
    }

    function get_sizes($image_id) {
        if (isset($image_id))
            $url = 'https://api.flickr.com/services/rest/?method=flickr.photos.getSizes&format=json&api_key=' . $this->api_key . '&nojsoncallback=1&photo_id=' . $image_id;
        else
            return false;

        $result = $this->request($url);
        return $result;
    }

    function validate_license($license) {
        $return = false;
        foreach ($this->post->getProject()->getPostTypeSpecificInfo()->licenses as $projectLicense) {
            if (trim($projectLicense) == $license) {
                $return = true;
            }
        }
        return $return;
    }

    function get_info($image_link) {
        preg_match_all('%/(?P<id>\d+)/%', $image_link, $match);
        if (isset($match['id'][0]))
            $url = 'https://api.flickr.com/services/rest/?method=flickr.photos.getInfo&format=json&api_key=' . $this->api_key . '&photo_id=' . $match['id'][0] . '&nojsoncallback=1';
        else
            return false;

        $result = $this->request($url);
        return $result;
    }

    function validate_duplicity($flickr_id) {
        #$images = db::query('select id from images where flickr_id = "'.$flickr_id.'" and project_id in ('.$this->article->project->related_projects.')');
        /*$related = ($this->article->project->related_projects != '') ? $this->article->project->related_projects : '0';
        //chequea que el mismo id de flickr no se encuentre en un proyecto relacionado o en un artículo de el mismo proyecto que ya haya sido escrito
        $sql = 'select * from images where flickr_id = ' . $flickr_id . ' 
	and (
		FIND_IN_SET(article_id, (
			select GROUP_CONCAT(id) from articles where project_id = ' . $this->article->project->id . ' and id not in (' . $this->article->id . ') and status_id > 2 group by project_id
		)) 
		or project_id in (' . $related . '
	    )
	);';
        $images = db::query($sql);

        if (is_array($images) and count($images) > 0)
            return false;
        else*/
            return true;
    }

}
