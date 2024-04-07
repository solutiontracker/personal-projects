<?php

namespace App\Http\Controllers\Thirdparty\Wordpress;


use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use \App\Mail\Email;

use App\Http\Controllers\Controller;

class UtilityController extends Controller
{
    protected $mailchimp;

    public function SendPdfEmail(Request $request) {
        $formInput = $request->all();

        info($request->all());
        
        $selectedTypes = explode(',',$formInput['selected']);

        $posts = DB::connection('wordpress_mysql')->table('eb_posts')->select(['post_title', 'post_content', 'ID', 'post_name'])->where('post_type', 'feature')->where('post_status', 'publish')->whereIn('post_name', $selectedTypes)->orderBy('post_type', 'desc')->get();
        
        $data=[];
        $i = 0;
        $j = 0;
        foreach ($posts as $key => $post) {
            $thumbnail_meta = DB::connection('wordpress_mysql')->table('eb_postmeta')->where('post_id', $post->ID)->where('meta_key', '_thumbnail_id')->get();
            $thumbnail_id = $thumbnail_meta[0]->meta_value;
            $thumbnail_post = DB::connection('wordpress_mysql')->table('eb_posts')->where('ID', $thumbnail_id)->get();
            $post->thumbnail_image_url = $thumbnail_post[0]->guid;
            $data[$i][$j] = $post;
            if($j == 1){
                $i++;
                $j = 0;
            }
            else{
                $j++;
            }
        }

        $html = \View::make('thirdParty.wordpress.feature-pdf.detail', compact('data'))->render();

        $file_to_save = config('cdn.cdn_upload_path').DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'ticket_'.time().'_'.rand(0,1000).'.pdf';
        if(file_exists($file_to_save)) { 
            \File::delete( $file_to_save ); 
        }
        \PDF::loadHTML($html)->setPaper('a4')
        // ->setOption('header-html',\View::make('thirdParty.wordpress.feature-pdf.header')->render())
        ->setOption("encoding","UTF-8")
        ->setOption('print-media-type', true)
        ->setOption('margin-right',0)
        ->setOption('margin-left',0)
        ->setOption('margin-bottom',0)
        ->setOption('margin-top',0)
        ->save($file_to_save);
        
        $file_to_save = $file_to_save;

        $data = array();
		$data['subject'] = "EventBuizz: Her er din featureliste";
        $data['attachment'] = [['path' => $file_to_save, 'name' => 'features.pdf']];
		$content = $data['content'] = "Kære ".$formInput['name']."<br/><br/>
 
Tak for din interesse for EventBuizz platformen.<br/><br/>

Vi har genereret en oversigt til dig med de features du har valgt så du nemt kan dele den med din arbejdsgruppe og netværk.<br/><br/>

Har du spørgsmål så kontakt os på sales@eventbuizz.com eller +45 6023 6666.<br/><br/>

Vi er nogen ildsjæle der elsker at hjælpe vores kunder.<br/>
<br/><br/>
 
<strong>Med venlig hilsen</strong><br/>
<strong>Team EventBuizz</strong><br/>
";
		$data['view'] = 'email.plain-text';
		\Mail::to($formInput['email'])->send(new Email($data));
        
$data['content'] = "Navn:".$formInput['name']."<br/>
Arbejdsmail:".$formInput['email']."<br/>
Telefon:".$formInput['phone']."<br/>
Skriv besked her:".$formInput['message']."<br/>
Page:".$formInput['url']."<br/><br/>
------------------------------------------------------
<br/>".$content."<br/>
------------------------------------------------------
";
        $emails = ['fa@eventbuizz.com', 'pso@eventbuizz.com', 'ki@eventbuizz.com','login@become.dk'];
        //$emails = ['ida@eventbuizz.com'];
        \Mail::to($emails)->send(new Email($data));
        
        return response()->json([
            'success' => true,
            'message' => 'Email successfully sent',
        ], 200);
    }
}
