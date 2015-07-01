<?php

class SearchController extends BaseController
{

    // SEARCH CONTROLLER :::

    public function getElementsByClassNamefun($elements, $className)
    {
        $matches = array();
        foreach ($elements as $element) {
            if (!$element->hasAttribute('class')) {
                continue;
            }
            $classes = preg_split('/\s+/', $element->getAttribute('class'));
            if (!in_array($className, $classes)) {
                continue;
            }
            $matches[] = $element;
            break;
        }
        return $matches;
    }

    public function getElementsByIdfun($elements, $className)
    {
        $matches = array();
        foreach ($elements as $element) {
            if (!$element->hasAttribute('id')) {
                continue;
            }
            $classes = preg_split('/\s+/', $element->getAttribute('id'));
            if (!in_array($className, $classes)) {
                continue;
            }
            $matches[] = $element;
            break;
        }
        return $matches;
    }

    //crawl contacts profiles from LinkedIn
    public function crawlLinkedin($CONTACTSO)
    {

        $argv = "";
        $_PROFILES = array();
        $CONTACTS = array();
        $work_history = array();
        $proxies = array('192.240.201.88', '23.95.96.151', '192.240.201.235', '107.182.125.4', '192.208.188.57', '192.227.240.74', '69.12.72.191', '192.3.111.222', '192.227.240.37', '23.95.21.97', '192.3.55.156', '216.107.157.79', '192.227.240.69', '192.3.111.157', '50.115.170.200', '192.240.201.9', '23.95.96.27', '198.12.91.144', '104.37.56.52', '167.160.127.178', '198.12.91.130', '192.208.188.29', '192.3.111.179', '23.95.96.153', '192.3.111.177', '192.3.111.236', '216.107.157.88', '69.12.72.201', '192.240.201.92', '192.3.55.203', '192.3.55.226', '198.12.91.232', '23.95.96.16', '50.115.171.2', '192.227.240.94', '192.227.240.93', '23.95.96.166', '23.95.21.234', '167.160.127.180', '192.3.55.239', '167.160.127.188', '167.160.112.60', '23.95.96.154', '198.50.25.158', '23.95.96.221', '192.208.188.228', '192.3.55.212', '23.95.96.162', '69.12.72.2', '216.107.157.201', '167.160.112.76', '192.227.240.53', '23.94.148.71', '107.182.125.69', '107.182.125.59', '192.3.55.231', '192.227.240.89', '192.208.188.49', '192.208.188.46', '192.3.55.175', '216.107.157.18', '198.50.25.162', '192.3.55.162', '107.182.125.62', '69.12.72.195', '216.107.157.80', '192.3.111.150', '192.208.188.6', '107.182.125.50', '167.160.112.29', '107.182.125.58', '107.182.125.229', '23.95.21.147', '192.3.111.194', '167.160.127.177', '23.95.21.92', '23.95.96.152', '192.3.111.234', '216.107.157.81', '216.107.157.173', '192.240.201.63', '69.12.72.193', '216.107.157.174', '69.12.72.197', '69.12.72.228', '23.95.96.19', '23.95.96.250', '69.12.72.20', '107.182.125.46', '167.160.127.216', '198.50.26.219', '192.208.188.31', '167.160.127.187', '192.3.55.220', '192.208.188.103', '192.227.240.72', '192.208.188.26', '192.3.55.137', '192.227.240.71', '69.12.72.198'); // Declaring an array to store the proxy list

       $proxy = $proxies[array_rand($proxies, 1)];
        //Log::notice("proxy=".$proxy);

        for ($ko = 0; $ko < count($CONTACTSO); $ko++) {

            //try{
            //$argv = $argv.str_replace("'","&#39;",$CONTACTSO[$ko]['linkedin'])." ";
            //$_PROFILES[$ko] = '';
            //$sURL = $CONTACTSO[$ko]['linkedin'];										

            $loginpassw = 'branb:O73bdu7';
            $proxy_ip = $proxy;
            $proxy_port = '80';
            $url = $CONTACTSO[$ko]['linkedin'];
            Log::notice($url);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_NTLM);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_PROXY, "$proxy:$proxy_port");
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, "$loginpassw");

            $data = curl_exec($ch);


            curl_close($ch);

            Log::notice($data);
            $_PROFILES[$ko] = $data;
            //Log::notice("Developer".$ko);
            //Log::notice($contents);
            //break;
            //}catch (Exception $e) {
            //Log::notice('File_Get_Contents Error:');
            //Log::notice($e->getMessage());
            //}
        }
       
        //Log::notice($_PROFILES);

        $y = 0;

        for ($ko = 0; $ko < count($CONTACTSO); $ko++) {

            $CONTACTSO[$ko]['www'] = '';
            $CONTACTSO[$ko]['member_id'] = '';
            $dom = new DOMDocument();
            $html = $_PROFILES[$ko];

            //Log::notice('HTML :'.$ko);
            //Log::notice($html);

            $member_id = null;
            $newTrkInfo = explode("newTrkInfo='", $html);

            if (count($newTrkInfo) > 1) {
                $newTrkInfo = explode(",'", $newTrkInfo[1]);
                if (count($newTrkInfo) > 1) {
                    $member_id = $newTrkInfo[0];
                }
            } else {
                $newTrkInfo = explode("newTrkInfo = '", $html);
                if (count($newTrkInfo) > 1) {
                    $newTrkInfo = explode(",'", $newTrkInfo[1]);
                    if (count($newTrkInfo) > 1) {
                        $member_id = $newTrkInfo[0];
                    }
                }
            }

            $CONTACTSO[$ko]['member_id'] = $member_id;

            @$dom->loadHTML($html);

            // scraping data from profile with different html tags
            $current_company = $this->getElementsByIdfun($dom->getElementsByTagName('tr'), 'overview-summary-current');
            $past_company = $this->getElementsByIdfun($dom->getElementsByTagName('tr'), 'overview-summary-past');
            $education = $this->getElementsByIdfun($dom->getElementsByTagName('tr'), 'overview-summary-education');
            $current_title = $this->getElementsByClassNamefun($dom->getElementsByTagName('p'), 'title');
            //$current_title   = $this->getElementsByClassNamefun($dom->getElementsByTagName('p'), 'span');
            $summary = $this->getElementsByClassNamefun($dom->getElementsByTagName('p'), 'description');
            $connections = $this->getElementsByClassNamefun($dom->getElementsByTagName('div'), 'member-connections');
            if (count($current_company)) {
                $CONTACTSO[$ko]['company_name'] = preg_replace('/\s+/', ' ', trim(strip_tags(str_replace('Current', '', $current_company[0]->textContent))));
                //Log::notice("company_result:". $CONTACTSO[$ko]['company_name']     );	
            } else {
                //Log::notice("company_result: not found");	
            }

            if (count($current_title)) {
                $CONTACTSO[$ko]['title'] = preg_replace('/\s+/', ' ', trim(strip_tags($current_title[0]->textContent)));
                //Log::notice("title_result:". $CONTACTSO[$ko]['title']     );	
            } else {
                //Log::notice("title_result: not found");	
            }

            if (count($past_company)) {
                $work_history[0] = preg_replace('/\s+/', ' ', trim(strip_tags(str_replace('Previous', '', $past_company[0]->textContent))));
                $CONTACTSO[$ko]['work_history'] = $work_history;
                //Log::notice("history_result:". $CONTACTSO[$ko]['company_name']     );	
            } else {
                //Log::notice("history_result: not found");	
            }

            if (count($education)) {
                $CONTACTSO[$ko]['education'] = preg_replace('/\s+/', ' ', trim(strip_tags(str_replace('Education', '', $education[0]->textContent))));
                //Log::notice("education_result:". $CONTACTSO[$ko]['education']     );	
            } else {
                //Log::notice("education_result: not found");	
            }

            if (count($summary)) {
                $CONTACTSO[$ko]['summary'] = preg_replace('/\s+/', ' ', trim(strip_tags($summary[0]->textContent)));
                //Log::notice("summary_result:". $CONTACTSO[$ko]['summary']     );	
            } else {
                //Log::notice("summary_result: not found");	
            }

            if (count($connections)) {
                $CONTACTSO[$ko]['connections'] = preg_replace('/\s+/', ' ', trim(strip_tags($connections[0]->textContent)));
                //Log::notice("connections_result:". $CONTACTSO[$ko]['connections']     );	
            } else {
                //Log::notice("connections_result: not found");	
            }
            // end

            //Log::notice($html);

            //$member = $this->getElementsByClassName($dom, '');
            $id = $dom->getElementById('overview');

            $dt = $dom->getElementsByTagName('dt');
            $dd_list = $dom->getElementsByTagName('dd');

            $i = 0;

            foreach ($dd_list as $dd) {

                if (trim($dt->item($i)->textContent) == "Location") {
                    $CONTACTSO[$ko]['location'] = preg_replace('/\s+/', ' ', trim(strip_tags($dd->textContent)));
                    //Log::notice("Location = ".$CONTACTSO[$ko]['location']);
                }

                if (trim($dt->item($i)->textContent) == "Industry") {
                    $CONTACTSO[$ko]['industry'] = preg_replace('/\s+/', ' ', trim(strip_tags($dd->textContent)));
                    //Log::notice("Industry = ".$CONTACTSO[$ko]['industry']);
                }
                $i++;
            }


            if (is_object($id)) {
                Log::notice("is_object");
                if ($id->hasChildNodes()) {

                    $dt = $id->getElementsByTagName('dt');
                    $dd_list = $id->getElementsByTagName('dd');

                    $i = 0;

                    Log::notice("count_dd_list = " . count($dd_list));
                    if (count($dd_list) > 0) {
                        foreach ($dd_list as $dd) {

                            if (trim($dt->item($i)->textContent) == "Current") {

                                $position = "";
                                $company = "";

                                $current = explode(" at ", preg_replace('/\s+/', ' ', $dd->getElementsByTagName('ul')->item(0)->getElementsByTagName('li')->item(0)->textContent));
                                if (count($current) < 2)
                                    $current = explode(" of ", preg_replace('/\s+/', ' ', $dd->getElementsByTagName('ul')->item(0)->getElementsByTagName('li')->item(0)->textContent));
                                if (count($current) > 1) {
                                    if (!empty($current[0])) $position = $current[0];
                                    if (!empty($current[1])) $company = $current[1];
                                }

                                if (count($current) < 2) $position = preg_replace('/\s+/', ' ', $dd->getElementsByTagName('ul')->item(0)->getElementsByTagName('li')->item(0)->textContent);

                                $CONTACTSO[$ko]['title'] = preg_replace('/\s+/', ' ', trim(strip_tags($position)));
                                $CONTACTSO[$ko]['company_name'] = preg_replace('/\s+/', ' ', trim(strip_tags($company)));
                                Log::notice($CONTACTSO[$ko]['title'] . " = " . $CONTACTSO[$ko]['company_name']);
                            }

                            if (trim($dt->item($i)->textContent) == "Education")
                                $CONTACTSO[$ko]['education'] = preg_replace('/\s+/', ' ', trim(strip_tags($dd->textContent)));

                            if (trim($dt->item($i)->textContent) == "Past") {
                                $_index = 0;
                                $_ul = $dd->getElementsByTagName('ul');
                                foreach ($_ul as $ul) {
                                    $_li = $ul->getElementsByTagName('li');
                                    foreach ($_li as $li) {
                                        $work_history[$_index] = trim(preg_replace('/\s+/', ' ', strip_tags($li->textContent)));
                                        $_index++;
                                        if ($_index > 4) break;
                                    }
                                    if ($_index > 4) break;
                                }

                                $CONTACTSO[$ko]['work_history'] = $work_history;

                            }

                            //$CONTACTSO[$ko]['work_history'] = preg_replace('/\s+/', ' ',trim(strip_tags($dd->textContent)));

                            if (trim($dt->item($i)->textContent) == "Websites") {

                                $d = 0;
                                $ul = $dd->getElementsByTagName('ul')->item(0)->getElementsByTagName('li');

                                foreach ($ul as $li) {

                                    $web = preg_replace('/\s+/', ' ', trim($li->textContent));
                                    $link = trim(urldecode($li->getElementsByTagName('a')->item(0)->getAttribute('href')));
                                    $link = explode("://", urldecode(trim($li->getElementsByTagName('a')->item(0)->getAttribute('href'))));

                                    if (!empty($link[1])) {

                                        $link = explode("&urlhash", $link[1]);
                                        if ($web == "Company Website") $CONTACTSO[$ko]['www'] = $CONTACTSO[$ko]['www'] . preg_replace('/\s+/', ' ', trim($link[0])) . " ";

                                    }

                                    $d++;
                                }

                            }

                            $i++;
                        }
                    }

                }

                $CONTACTSO[$ko]['www'] = trim($CONTACTSO[$ko]['www']);

                $CONTACTSO[$ko]['linkedin'] = base64_encode($CONTACTSO[$ko]['linkedin']);


            } else {

                $CONTACTSO[$ko]['linkedin'] = base64_encode($CONTACTSO[$ko]['linkedin']);

            }

            if ($CONTACTSO[$ko]['title'] != '' && $CONTACTSO[$ko]['company_name'] != '') {
                $CONTACTS[$y] = $CONTACTSO[$ko];
                $y++;
            } else if ($CONTACTSO[$ko]['title'] != '' && $CONTACTSO[$ko]['company_name'] == '') {
                $CONTACTS[$y] = $CONTACTSO[$ko];
                $y++;
            } else if ($CONTACTSO[$ko]['title'] == '' && $CONTACTSO[$ko]['company_name'] == '') {
                $CONTACTS[$y] = $CONTACTSO[$ko];
                $y++;
            }
        }

        //Log::notice($CONTACTS);

        return $CONTACTS;

    }

    //parse html content of company profile from LinkedIn
    public function crawlCompany($profile)
    {

        $website = "";

        $dom = new DOMDocument();

        @$dom->loadHTML($profile);
        $id = $dom->getElementById('extra');


        if (is_object($id)) {

            $dt = $id->getElementsByTagName('dt');
            $dd_list = $id->getElementsByTagName('dd');

            $i = 0;

            foreach ($dd_list as $dd) {

                if (trim($dt->item($i)->textContent) == "Website") {

                    $address = trim(strip_tags($dd->textContent));
                    $address = explode("://", $address);

                    if (!empty($address[1])) {

                        $address = explode("/", $address[1]);

                        if (!empty($address[0])) {

                            $website = $website . $address[0] . " ";

                        }

                    }

                }

                $i++;
            }

        }

        return trim($website);
    }

    //search company profiles by "company name" on google and crawl their profiles from LinkedIn
    public function searchCompany($company_name)
    {

        $website = array();
        $argv_index = '';
        $argv_link = array();
        $_PROFILES = array();

        $config = Config::get('API');

        if ($company_name != '') {

            set_time_limit(0);

            $input = urlencode($company_name);

            $url = $config['api_google']['url'] . "/cse?";
            $url = $url . "cx=" . $config['api_google']['token'];//005430263908391132603:_vta5wxoapo
            $url = $url . "&client=google-csbe";
            $url = $url . "&output=xml_no_dtd";
            $url = $url . "&lr=lang_en";
            $url = $url . "&hl=en";
            $url = $url . "&start=0&num=10";
            $url = $url . "&as_qdr=all";
            $url = $url . "&gl=uk+OR+us";
            $url = $url . "&q=" . $input . "+";
            $url = $url . urlencode("site:linkedin.com/company/") . "+" . urlencode("-site:linkedin.com/in/") . "+" . urlencode("-site:linkedin.com/pub/") . "+" . urlencode("-site:linkedin.com/pub/dir/") . "+" . urlencode("-site:linkedin.com/title/");
            $google = file_get_contents($url);
            $google = simplexml_load_string($google);

            if (count($google->RES->R) > 0) {

                $k = 0;
                foreach ($google->RES->R as $R) {

                    $argv_link[$k] = str_replace("'", "&#39;", $R->U);
                    $k++;

                }

            }

        }

        if (count($argv_link) > 0) {

            ob_start();
            $config = Config::get('crawler');
            passthru($config['python'] . ' ' . $config['script'] . '/app/python/crawl_company_profile.py ' . trim(implode(' ', $argv_link)));
            $_PROFILES_STRING = ob_get_clean();
            //close connection

            $_PROFILES_ARRAY = explode("ABCDEFGH123456789HGFEDCBA", $_PROFILES_STRING);

            $y = 0;
            for ($ko = 0; $ko < count($_PROFILES_ARRAY); $ko++) {

                $_www_linkedin = explode("HGFEDCBA123456789ABCDEFGH", $_PROFILES_ARRAY[$ko]);

                if (empty($_www_linkedin[1])) {
                    $_www_linkedin[1] = '';
                }

                $key = explode('www.', $this->crawlCompany($_www_linkedin[0]));

                if (!empty($key[1])) {

                    $key = $key[1];

                } else $key = $key[0];

                if ($_www_linkedin[1] != "") {

                    $website[$y] = array();
                    $website[$y]["linkedin_url"] = $_www_linkedin[1];
                    $website[$y]["url"] = strtolower($key);
                    $y++;

                }

            }

        }

        return $website;

    }

    //sort bidimensional array by column
    public static function array_orderby()
    {

        $args = func_get_args();
        $data = array_shift($args);

        foreach ($args as $n => $field) {

            if (is_string($field)) {

                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;

            }

        }

        $args[] = &$data;
        call_user_func_array('array_multisort', $args);

        return array_pop($args);

    }

    function check_matches($data, $array_of_needles)
    {
        foreach ($array_of_needles as $needle) {
            if (stripos($data, $needle) !== FALSE) {
                return true;
            }
        }

        return false;
    }

    public function saveLinkedinSearch()
    {

        $_JSON['code'] = '';
        $_JSON['error'] = '';
        $_JSON['msg'] = '';

        $CONTACTS = array();
        $CONTACTS_TO_SAVE = array();
        $contacts = array();
        $_contact = array();
        $_CONTACT = array();
        $k = 0;

        if (Auth::check()) {

            $user = User::find(Auth::user()->id);

            if (Input::get('myArray', '') != '') {

                $dataArray = Input::get('myArray');
                $dataArray = array_reverse($dataArray);

                foreach ($dataArray as $row) {

                    $_contact['action'] = '';
                    $_contact['action_id'] = '';
                    $_contact['photo'] = $row['photo'];
                    $_contact['firstname'] = $row['firstname'];
                    $_contact['lastname'] = $row['lastname'];
                    $_contact['name'] = $row['fullname'];
                    $_contact['title'] = $row['title'];
                    $_contact['company_name'] = $row['company_name'];
                    $_contact['location'] = $row['location_add'];
                    $_contact['education'] = '';
                    $_contact['work_history2'] = '';
                    $_contact['work_history'] = array();
                    $_contact['www'] = '';
                    $_contact['company_linkedin_www'] = '';
                    $_contact['www_variants'] = '';
                    $_contact['variants'] = '';
                    $_contact['linkedin'] = $row['linkedin_url'];
                    $_contact['member_id'] = $row['member_id'];
                    $_contact['industry'] = '';
                    $_contact["summary"] = $row['description'];
                    $_contact['connections'] = '';

                    $CONTACTS = array_add($CONTACTS, $k, $_contact);
                    $CONTACTS_TO_SAVE = array_add($CONTACTS_TO_SAVE, $k, $_contact);
                    $k++;
                }

                //$CONTACTS = $this->crawlLinkedin($CONTACTS);

                for ($k = 0; $k < count($CONTACTS); $k++) {

                    $CONTACTS[$k]['linkedin'] = base64_encode($CONTACTS[$k]['linkedin']);
                    if ($CONTACTS[$k]['photo'] != "") $CONTACTS[$k]['photo'] = base64_encode($CONTACTS[$k]['photo']);
                    $CONTACTS_TO_SAVE[$k]["work_history"] = $CONTACTS[$k]["work_history"];
                    $CONTACTS_TO_SAVE[$k]["title"] = $CONTACTS[$k]["title"];
                    $CONTACTS_TO_SAVE[$k]["company_name"] = $CONTACTS[$k]["company_name"];
                    $CONTACTS_TO_SAVE[$k]["location"] = $CONTACTS[$k]["location"];
                    $CONTACTS_TO_SAVE[$k]["member_id"] = $CONTACTS[$k]["member_id"];
                }

                $user->cache = serialize($CONTACTS);
                $user->save();

                $_list = Lists::where('user_id', '=', Auth::user()->id)->first();
                $_list = unserialize($_list->cache);

                $this->saveLinkedinContact($CONTACTS_TO_SAVE);

                for ($k = 0; $k < count($CONTACTS); $k++) {
                    $responseData = $this->purchaseAllContact($k);
                    if (isset($responseData['error'])) {
                        $_JSON['error'] = $responseData['error'];
                        if ($responseData['error'] != '') {
                            return Response::json($_JSON);
                            exit();
                        }
                    }
                }
            }
            $_JSON['TotalRecords'] = count($CONTACTS);
            $_JSON['TotalDisplayRecords'] = count($CONTACTS);
            $_JSON['aaData'] = $CONTACTS;
        } else {
            $_JSON['error'] = '';
            $_JSON['code'] = '0001';
        }

        return Response::json($_JSON);
    }

    //search contacts on google
    public function jsonGoogleNew()
    {
        $_JSON['limit'] = Input::get('limit');
        $_JSON['TotalRecords'] = Session::get('count');
        $_JSON['TotalDisplayRecords'] = Session::get('count');
        $_JSON['code'] = '';
        $_JSON['error'] = '';
        $_JSON['msg'] = '';

        $start = Input::get('start', '0');
        $length = Input::get('length', '20');
        $num = Input::get('length', '20');

        $CONTACTS = array();
        $CONTACTS_TO_SAVE = array();
        $_contact = array();

        $_JSON['aaData'] = array();
        $config = Config::get('API');

        if (Auth::check()) {

            //try {
            $user = User::find(Auth::user()->id);
            $_JSON['TotalRecords'] = 0;
            $_JSON['TotalDisplayRecords'] = 0;

            if (Input::get('sSearch', '') != '') {

                if (Input::get('sSearch') != Session::get('sSearch')) {
                    Session::put('sSearch', Input::get('sSearch'));
                    Session::save();
                    $user->cache = '';
                }

                $k = 0;
                $input = '';
                $sSearch = explode(",", Input::get('sSearch'));

                if (Input::get('search_type', 'basic') == "basic") {

                    foreach ($sSearch as $s) {
                        $input = $input . urlencode(trim(strip_tags($s))) . '+';
                    }
                }

                $url = $config['api_google']['url'] . "/cse?";
                $url = $url . "cx=" . $config['api_google']['token'];
                $url = $url . "&client=google-csbe";
                $url = $url . "&output=xml_no_dtd";
                $url = $url . "&lr=lang_en";
                $url = $url . "&hl=en";
                $url = $url . "&start=" . $start . "&num=" . $num;
                $url = $url . "&as_qdr=all";
                $url = $url . "&gl=uk+OR+us";

                if (Input::get('search_type') == "advanced") {
                    $url = $url . "&q=" . Input::get('sSearch', '');
                } else {
                    $url = $url . "&q=" . $input . urlencode("site:linkedin.com/in/") . "+OR+" . urlencode("linkedin.com/pub/") . "+" . urlencode("-site:linkedin.com/pub/dir/") . "+" . urlencode("-site:linkedin.com/groups/");
                }

                $_JSON['url'] = $url;
                $output = file_get_contents($url);
                $GSP = simplexml_load_string($output);

                foreach ($GSP->RES->R as $R) {

                    if ($R["N"] != 0) {

                        $extract1 = explode("|", $R->T);
                        if (count($extract1) > 0) {
                            $extract2 = explode(" ", $extract1[0]);
                        } else {
                            $extract1 = explode("-", $R->T);
                            $extract2 = explode(" ", $extract1[0]);
                        }
                        $linkedin_url = (string)$R->U;

                        if (strpos($linkedin_url, 'www.linkedin.com') !== false) {
                            //echo 'Yes, the string found in the above sentence';
                        } else {
                            //echo ' No, the string doesnot exist in the sentence' ;
                            continue;
                        }

                        $summary = (string)$R->S;

                        $firstname = "";
                        $lastname = "";
                        if (count($extract2) > 1) {
                            if (!empty($extract2[0])) $firstname = trim(strip_tags($extract2[0]));
                            if (!empty($extract2[1])) $lastname = trim(strip_tags($extract2[1]));
                        }

                        $photo = '';
                        $location = '';
                        $title = '';
                        $company = '';
                        $title_company = '';
                        $education = '';
                        $previous_role = '';
                        $work_history = array();
                        $work_history2 = '';
                        $prefix1 = '';
                        $prefix2 = '';

                        $_JSON['msg'] = (string)$R["N"];

                        if (count($R->PageMap->DataObject) > 0) {

                            foreach ($R->PageMap->DataObject as $DataObject) {

                                if (!empty($DataObject['type'])) {

                                    if ($DataObject['type'] == 'cse_thumbnail') {
                                        foreach ($DataObject->Attribute as $Attribute) {
                                            if (!empty($Attribute['name'])) {
                                                if ($Attribute['name'] == 'src') {
                                                    $photo = (string)$Attribute['value'];
                                                }
                                            }
                                        }
                                    }

                                    if ($DataObject['type'] == 'person') {
                                        foreach ($DataObject->Attribute as $Attribute) {
                                            if (!empty($Attribute['name'])) {
                                                if ($Attribute['name'] == 'location') {
                                                    $location = $Attribute['value'];
                                                }
                                                if ($Attribute['name'] == 'role') {
                                                    $title_company = $Attribute['value'];
                                                    $title_company = explode(" at ", $title_company);
                                                    if (count($title_company) > 1) {
                                                        if (!empty($title_company[0])) $title = $title_company[0];
                                                        if (!empty($title_company[1])) $company = $title_company[1];
                                                    } else {
                                                        $title_company = $Attribute['value'];
                                                        $title_company = explode(" of ", $title_company);
                                                        if (count($title_company) > 1) {
                                                            if (!empty($title_company[0])) $title = $title_company[0];
                                                            if (!empty($title_company[1])) $company = $title_company[1];
                                                        } else {
                                                            $title_company = $Attribute['value'];
                                                            $title_company = explode(" @ ", $title_company);
                                                            if (count($title_company) > 1) {
                                                                if (!empty($title_company[0])) $title = $title_company[0];
                                                                if (!empty($title_company[1])) $company = $title_company[1];
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    $total_attributes = count($DataObject->Attribute);

                                    if ($DataObject['type'] == 'hcard') {
                                        foreach ($DataObject->Attribute as $Attribute) {
                                            if (!empty($Attribute['name'])) {
                                                if ($Attribute['name'] == 'fn') {
                                                    if ($total_attributes == 2) {
                                                        $hcard_array = json_decode(json_encode($Attribute->attributes()->value), TRUE);

                                                        $array_of_needles = array('University', 'College', 'Institute', 'Academy', 'School', 'Universidade');

                                                        if ($this->check_matches($hcard_array[0], $array_of_needles)) {
                                                            $education .= $prefix1 . $hcard_array[0];
                                                            $prefix1 = ',';
                                                        } else {
                                                            $work_history2 .= $prefix2 . $hcard_array[0];
                                                            $prefix2 = ',';
                                                        }
                                                    }
                                                }
                                                if ($Attribute['name'] == 'title') {
                                                    $previous_role = json_decode(json_encode($Attribute->attributes()->value), TRUE);
                                                    $previous_role = $previous_role[0];
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            if (!empty($title)) {
                                $title = trim(strip_tags($title));
                            } else {
                                $title = $previous_role;
                            }
                            if (!empty($location)) $location = trim(strip_tags($location));

                            $_contact['action'] = '';
                            $_contact['action_id'] = '';
                            $_contact['photo'] = $photo;
                            $_contact['firstname'] = $firstname;
                            if (empty(trim($lastname))) {
                                $_contact['name'] = $firstname;
                            } else {
                                $_contact['name'] = $firstname . " " . $lastname;
                            }
                            $_contact['lastname'] = $lastname;
                            $_contact['title'] = $title;
                            $_contact['company_name'] = $company;
                            $_contact['location'] = $location;
                            $_contact['education'] = $education;
                            $_contact['previous'] = '';
                            $_contact['work_history2'] = $work_history2;
                            $_contact['work_history'] = array();
                            $_contact['www'] = '';
                            $_contact['company_linkedin_www'] = '';
                            $_contact['www_variants'] = '';
                            $_contact['variants'] = '';
                            $_contact['linkedin'] = $linkedin_url;
                            $_contact['member_id'] = '';
                            $_contact['industry'] = '';
                            $_contact["summary"] = str_ireplace("<br>", "", $summary);
                            $_contact["summary"] = str_ireplace("<br/>", "", $summary);
                            $_contact["summary"] = str_ireplace("<br />", "", $summary);
                            $_contact['connections'] = '';

                            $CONTACTS = array_add($CONTACTS, $k, $_contact);

                            $CONTACTS_TO_SAVE = array_add($CONTACTS_TO_SAVE, $k, $_contact);

                            $k++;
                        }
                    }
                }

                for ($k = 0; $k < count($CONTACTS); $k++) {

                    $CONTACTS[$k]['linkedin'] = base64_encode($CONTACTS[$k]['linkedin']);
                    if ($CONTACTS[$k]['work_history2'] != '' and !is_array($CONTACTS[$k]['work_history2'])) {
                        $CONTACTS[$k]['work_history2'] = explode(",", $CONTACTS[$k]['work_history2']);
                        $CONTACTS[$k]['work_history2'] = array_unique($CONTACTS[$k]['work_history2']);
                    }
                    if ($CONTACTS[$k]['education'] != '' and !is_array($CONTACTS[$k]['education'])) {
                        $CONTACTS[$k]['education'] = explode(",", $CONTACTS[$k]['education']);
                        $CONTACTS[$k]['education'] = array_unique($CONTACTS[$k]['education']);
                    }
                    //$CONTACTS[$k]["linkedin_html"] = '<a href="'.$CONTACTS[$k]["link"].'" target="_blank"><img src="/resources/images/icons/linkedin_table.png"/></a>';								
                    if ($CONTACTS[$k]['photo'] != "") $CONTACTS[$k]['photo'] = base64_encode($CONTACTS[$k]['photo']);
                    $CONTACTS_TO_SAVE[$k]["work_history"] = $CONTACTS[$k]["work_history"];
                    $CONTACTS_TO_SAVE[$k]["title"] = $CONTACTS[$k]["title"];
                    $CONTACTS_TO_SAVE[$k]["company_name"] = $CONTACTS[$k]["company_name"];
                    $CONTACTS_TO_SAVE[$k]["location"] = $CONTACTS[$k]["location"];
                    $CONTACTS_TO_SAVE[$k]["member_id"] = $CONTACTS[$k]["member_id"];
                }

                if ($user->cache != '') {
                    $OLDCONTACTS = unserialize($user->cache);
                    $CONTACTS = array_merge($CONTACTS, $OLDCONTACTS);
                } else {

                }

                $user->cache = serialize($CONTACTS);
                $user->save();

                //if($user->cache!='') {} $CONTACTS = unserialize($user->cache);

                $_list = Lists::where('user_id', '=', Auth::user()->id)->first();

                $_list = unserialize($_list->cache);

                $linkeinURL = array();

                for ($t = 0; $t < count($_list); $t++) {
                    $linkeinURL[$t] = $_list[$t]["linkedin"];
                }

                for ($k = 0; $k < count($CONTACTS); $k++) {

                    // Log::notice($CONTACTS[$k]["photo"]);

                    if ($CONTACTS[$k]["photo"] != '') {
                        $CONTACTS[$k]["photo"] = '<a href="' . base64_decode($CONTACTS[$k]["linkedin"]) . '" target="_blank"><img class="linkedin" style="height:80px; width:80px;" src="' . base64_decode($CONTACTS[$k]["photo"]) . '"/></a>';
                    } else {
                        $CONTACTS[$k]["photo"] = '<a href="' . base64_decode($CONTACTS[$k]["linkedin"]) . '" target="_blank" style="width:80px; height:80px;">&nbsp;</a>';
                    }

                    $linkedin_png = '<a href="' . base64_decode($CONTACTS[$k]["linkedin"]) . '" target="_blank"><img src="/resources/images/icons/linkedin_table.png"/></a>';
                    $CONTACTS[$k]['social'] = $linkedin_png;
                    $CONTACTS[$k]['firstname'] = $CONTACTS[$k]['firstname'] . ' ' . $CONTACTS[$k]['lastname'] . '<br>' . $linkedin_png;

                    if (is_array($CONTACTS[$k]['work_history']) and count($CONTACTS[$k]['work_history']) > 0) {
                        $work_history_html = "<ul>";
                        foreach ($CONTACTS[$k]['work_history'] as $work_history_item) {
                            $work_history_html .= "<li>" . $work_history_item . "</li>";
                        }
                        $work_history_html .= "</ul>";
                        $CONTACTS[$k]['work_history'] = $work_history_html;
                    }


                    if (is_array($CONTACTS[$k]['education']) and count($CONTACTS[$k]['education']) > 0) {
                        $work_history_html2 = "<ul>";
                        foreach ($CONTACTS[$k]['education'] as $work_history_item2) {
                            $work_history_html2 .= "<li style='margin-left:20px;'>" . $work_history_item2 . "</li>";
                        }
                        $work_history_html2 .= "</ul>";
                        $CONTACTS[$k]['education'] = $work_history_html2;
                    }

                    if (in_array($CONTACTS[$k]['linkedin'], $linkeinURL)) {

                        $item_id = 0;
                        for ($t = 0; $t < count($_list); $t++) {

                            if ($CONTACTS[$k]['linkedin'] == $_list[$t]["linkedin"]) {
                                $item_id = $_list[$t]["action"];
                                $CONTACTS[$k]['action'] = '<div class="added_btn" style="vertical-align: middle; display: table-cell;"><a class="btn btn-primary btn-lg pull-right" title="Added"><span class="added">Added</span><span class="view">View</span></a></div><input type="hidden" name="api_contact_id" value="' . $item_id . '" />';
                                $CONTACTS[$k]['action_id'] = $item_id;
                                break;
                            }
                        }

                    } else
                        $CONTACTS[$k]['action'] = '<div class="add_btn" style="vertical-align: middle; display: table-cell;"><a id="item_' . $k . '" class="btn btn-primary btn-lg pull-right" title="Add" data-toggle="popover" data-placement="left">Add</a></div><input type="hidden" name="api_contact_id" value="' . $k . '" /><div class="invisible fa fa-refresh fa-spin add-result-' . $k . '"></div>';
                    $CONTACTS[$k]['action_id'] = $k;

                }

                $this->saveLinkedinContact($CONTACTS_TO_SAVE);
                $start = $length;
                $length = $length + 20;
            } else {
                //added by developer for displaying add lead contact via chrome ext.								   
                if ($user->cache != '') $CONTACTS = unserialize($user->cache);

                $_list = Lists::where('user_id', '=', Auth::user()->id)->first();
                $_list = unserialize($_list->cache);
                $linkeinURL = array();
                $linkeinID = array();

                for ($t = 0; $t < count($_list); $t++) {
                    $linkeinURL[$t] = $_list[$t]["linkedin"];
                }
                for ($k = 0; $k < count($CONTACTS); $k++) {

                    //if(!isset($CONTACTS[$k]["link"])){$CONTACTS[$k]["link"]='';}
                    if (!isset($CONTACTS[$k]["summary"])) {
                        $CONTACTS[$k]["summary"] = '';
                    }

                    $CONTACTS[$k]["summary"] = str_ireplace("<br>", "", $CONTACTS[$k]["summary"]);
                    $CONTACTS[$k]["summary"] = str_ireplace("<br/>", "", $CONTACTS[$k]["summary"]);
                    $CONTACTS[$k]["summary"] = str_ireplace("<br />", "", $CONTACTS[$k]["summary"]);

                    //$CONTACTS[$k]["linkedin_html"] = '<a href="'.$CONTACTS[$k]["link"].'" target="_blank"><img src="/resources/images/icons/linkedin_table.png"/></a>';

                    if ($CONTACTS[$k]["photo"] != '') {
                        $CONTACTS[$k]["photo"] = '<a href="' . base64_decode($CONTACTS[$k]["linkedin"]) . '" target="_blank"><img class="linkedin" style="height:80px; width:80px;" src="' . base64_decode($CONTACTS[$k]["photo"]) . '"/></a>';
                    } else {
                        $CONTACTS[$k]["photo"] = '<a href="' . base64_decode($CONTACTS[$k]["linkedin"]) . '" target="_blank" style="width:80px; height:80px;">&nbsp;</a>';
                    }
                    $linkedin_png = '<a href="' . base64_decode($CONTACTS[$k]["linkedin"]) . '" target="_blank"><img src="/resources/images/icons/linkedin_table.png"/></a>';
                    $CONTACTS[$k]['social'] = $linkedin_png;
                    $CONTACTS[$k]['firstname'] = $CONTACTS[$k]['firstname'] . ' ' . $CONTACTS[$k]['lastname'] . '<br>' . $linkedin_png;
                    /*if($CONTACTS[$k]['work_history'] != '' and !is_array($CONTACTS[$k]['work_history'])){
										$CONTACTS[$k]['work_history'] = explode(",", $CONTACTS[$k]['work_history']);
									   }*/
                    if (is_array($CONTACTS[$k]['work_history'])) {
                        $work_history_html = "<ul>";
                        foreach ($CONTACTS[$k]['work_history'] as $work_history_item) {
                            $work_history_html .= "<li>" . $work_history_item . "</li>";
                        }
                        $work_history_html .= "</ul>";
                        $CONTACTS[$k]['work_history'] = $work_history_html;
                    }
                    if (in_array($CONTACTS[$k]['linkedin'], $linkeinURL)) {
                        $item_id = 0;
                        for ($t = 0; $t < count($_list); $t++) {
                            if ($CONTACTS[$k]['linkedin'] == $_list[$t]["linkedin"]) {
                                $item_id = $_list[$t]["action"];
                                $CONTACTS[$k]['action'] = '<div class="added_btn" style="vertical-align: middle; display: table-cell;"><a class="btn btn-primary btn-lg pull-right" title="Added"><span class="added">Added</span><span class="view">View</span></a></div><input type="hidden" name="api_contact_id" value="' . $item_id . '" />';
                                $CONTACTS[$k]['action_id'] = $item_id;
                                break;
                            }
                        }
                    } else
                        $CONTACTS[$k]['action'] = '<div class="add_btn" style="vertical-align: middle; display: table-cell;"><a id="item_' . $k . '" class="btn btn-primary btn-lg pull-right" title="Add" data-toggle="popover" data-placement="left">Add</a></div><input type="hidden" name="api_contact_id" value="' . $k . '" /><div class="invisible fa fa-refresh fa-spin add-result-' . $k . '"></div>';

                    $CONTACTS[$k]['action_id'] = $k;
                }
            }


            $_JSON['TotalRecords'] = count($CONTACTS);
            $_JSON['TotalDisplayRecords'] = count($CONTACTS);

            $_JSON['start'] = $start;
            $_JSON['length'] = $length;

            $_JSON['aaData'] = $CONTACTS;
            //}
            /*catch (Exception $e) {
                   $_JSON['error']   = $e->getMessage()." , ".$e->getLine();
                   $_JSON['code']    = '0002';
            }*/

        } else {
            $_JSON['error'] = '';
            $_JSON['code'] = '0001';
        }

        return Response::json($_JSON);
    }

    //search contacts on google
    public function jsonGoogle()
    {

        $_JSON['sEcho'] = Input::get('sEcho');
        $_JSON['iDisplayStart'] = Input::get('iDisplayStart');
        $_JSON['iDisplayLength'] = Input::get('iDisplayLength');
        $_JSON['iTotalRecords'] = Session::get('count');
        $_JSON['iTotalDisplayRecords'] = Session::get('count');
        $_JSON['code'] = '';
        $_JSON['error'] = '';
        $_JSON['msg'] = '';

        if (Input::get('sEcho') > 1) {
            $start = Input::get('sEcho') * 10;
            $num = 10 + $start;
        } else {
            $start = 0;
            $num = 10;
        }

        $CONTACTS = array();
        $CONTACTS_TO_SAVE = array();
        $contacts = array();
        $_contact = array();
        $_CONTACT = array();

        $allow_sort = 1;

        $_JSON['aaData'] = array();
        $config = Config::get('API');

        if (Auth::check()) {

            //try {
            $user = User::find(Auth::user()->id);
            $iSortCol = Input::get('iSortCol_0');

            if ($iSortCol < 2) $iSortCol = 2;

            $_JSON['iTotalRecords'] = 0;
            $_JSON['iTotalDisplayRecords'] = 0;

            if (Input::get('sSearch', '') != '') {

                if (Input::get('sSearch') != Session::get('sSearch')) {

                    Session::put('sSearch', Input::get('sSearch'));
                    Session::save();
                    $user->cache = '';
                }
                $k = 0;

                $input = '';

                $sSearch = explode(",", Input::get('sSearch'));

                if (Input::get('search_type', 'basic') == "basic") {

                    foreach ($sSearch as $s) {
                        $input = $input . urlencode(trim(strip_tags($s))) . '+';
                    }

                }

                $allow_sort = 0;

                $url = $config['api_google']['url'] . "/cse?";
                $url = $url . "cx=" . $config['api_google']['token'];//005430263908391132603:_vta5wxoapo
                $url = $url . "&client=google-csbe";
                $url = $url . "&output=xml_no_dtd";
                $url = $url . "&lr=lang_en";
                $url = $url . "&hl=en";
                //$url = $url."&start=0&num=20";
                $url = $url . "&start=" . $start . "&num=" . $num;
                $url = $url . "&as_qdr=all";
                $url = $url . "&gl=uk+OR+us";

                if (Input::get('search_type') == "advanced") {
                    $url = $url . "&q=" . Input::get('sSearch', '');
                } else {
                    $url = $url . "&q=" . $input . urlencode("site:linkedin.com/in/") . "+OR+" . urlencode("linkedin.com/pub/") . "+" . urlencode("-site:linkedin.com/pub/dir/") . "+" . urlencode("-site:linkedin.com/groups/");
                }

                $_JSON['url'] = $url;
                $output = file_get_contents($url);
                $GSP = simplexml_load_string($output);

                foreach ($GSP->RES->R as $R) {
                    //------------------------------------------------------------------------------

                    if ($R["N"] != 0) {

                        $extract1 = explode("-", $R->T);
                        if (count($extract1) > 0) {
                            $extract2 = explode(" ", $extract1[0]);
                        } else {
                            $extract1 = explode("|", $R->T);
                            $extract2 = explode(" ", $extract1[0]);
                        }
                        $linkedin_url = (string)$R->U;

                        $summary = (string)$R->S;

                        $firstname = "";
                        $lastname = "";
                        if (count($extract2) > 1) {
                            if (!empty($extract2[0])) $firstname = trim(strip_tags($extract2[0]));
                            if (!empty($extract2[1])) $lastname = trim(strip_tags($extract2[1]));
                        }

                        $photo = '';
                        $location = '';
                        $title = '';
                        $company = '';
                        $title_company = '';
                        $education = '';
                        $previous_role = '';
                        //$work_history  = array();
                        $work_history = '';
                        $prefix1 = '';
                        $prefix2 = '';

                        $_JSON['msg'] = (string)$R["N"];

                        if (count($R->PageMap->DataObject) > 0) {

                            foreach ($R->PageMap->DataObject as $DataObject) {

                                if (!empty($DataObject['type'])) {

                                    if ($DataObject['type'] == 'cse_thumbnail') {
                                        foreach ($DataObject->Attribute as $Attribute) {
                                            if (!empty($Attribute['name'])) {
                                                if ($Attribute['name'] == 'src') {
                                                    $photo = (string)$Attribute['value'];
                                                }
                                            }
                                        }
                                    }

                                    if ($DataObject['type'] == 'person') {
                                        foreach ($DataObject->Attribute as $Attribute) {
                                            if (!empty($Attribute['name'])) {
                                                if ($Attribute['name'] == 'location') {
                                                    $location = $Attribute['value'];
                                                }
                                                if ($Attribute['name'] == 'role') {
                                                    $title_company = $Attribute['value'];
                                                    $title_company = explode(" at ", $title_company);
                                                    if (count($title_company) > 1) {
                                                        if (!empty($title_company[0])) $title = $title_company[0];
                                                        if (!empty($title_company[1])) $company = $title_company[1];
                                                    } else {
                                                        $title_company = $Attribute['value'];
                                                        $title_company = explode(" of ", $title_company);
                                                        if (count($title_company) > 1) {
                                                            if (!empty($title_company[0])) $title = $title_company[0];
                                                            if (!empty($title_company[1])) $company = $title_company[1];
                                                        } else {
                                                            $title_company = $Attribute['value'];
                                                            $title_company = explode(" @ ", $title_company);
                                                            if (count($title_company) > 1) {
                                                                if (!empty($title_company[0])) $title = $title_company[0];
                                                                if (!empty($title_company[1])) $company = $title_company[1];
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    $total_attributes = count($DataObject->Attribute);

                                    if ($DataObject['type'] == 'hcard') {
                                        foreach ($DataObject->Attribute as $Attribute) {
                                            if (!empty($Attribute['name'])) {
                                                if ($Attribute['name'] == 'fn') {
                                                    if ($total_attributes == 2) {
                                                        $hcard_array = json_decode(json_encode($Attribute->attributes()->value), TRUE);

                                                        $array_of_needles = array('University', 'College', 'Institute', 'Academy', 'School', 'Universidade');

                                                        if ($this->check_matches($hcard_array[0], $array_of_needles)) {
                                                            $education .= $prefix1 . $hcard_array[0];
                                                            $prefix1 = ',';
                                                        } else {
                                                            $work_history .= $prefix2 . $hcard_array[0];
                                                            $prefix2 = ',';
                                                        }
                                                    }
                                                }
                                                if ($Attribute['name'] == 'title') {
                                                    $previous_role = json_decode(json_encode($Attribute->attributes()->value), TRUE);
                                                    $previous_role = $previous_role[0];
                                                }
                                            }
                                        }
                                    }

                                }

                            }

                            if (!empty($title)) {
                                $title = trim(strip_tags($title));
                            } else {
                                $title = $previous_role;
                            }
                            if (!empty($location)) $location = trim(strip_tags($location));

                            $_contact['action'] = '';
                            $_contact['action_id'] = '';
                            $_contact['photo'] = $photo;
                            $_contact['firstname'] = $firstname;
                            $_contact['name'] = $firstname;
                            $_contact['lastname'] = $lastname;
                            $_contact['title'] = $title;
                            $_contact['company_name'] = $company;
                            $_contact['location'] = $location;
                            $_contact['education'] = $education;
                            $_contact['work_history'] = $work_history;
                            //$_contact['work_history']  = array();
                            $_contact['www'] = '';
                            $_contact['company_linkedin_www'] = '';
                            $_contact['www_variants'] = '';
                            $_contact['variants'] = '';
                            $_contact['linkedin'] = $linkedin_url;
                            $_contact['member_id'] = '';
                            $_contact['industry'] = '';
                            $_contact['summary'] = $summary;
                            $_contact['connections'] = '';

                            $CONTACTS = array_add($CONTACTS, $k, $_contact);

                            $CONTACTS_TO_SAVE = array_add($CONTACTS_TO_SAVE, $k, $_contact);

                            $k++;
                        }
                    }
                }


                for ($k = 0; $k < count($CONTACTS); $k++) {

                    $CONTACTS[$k]['linkedin'] = base64_encode($CONTACTS[$k]['linkedin']);
                    if ($CONTACTS[$k]['work_history'] != '' and !is_array($CONTACTS[$k]['work_history'])) {
                        $CONTACTS[$k]['work_history'] = explode(",", $CONTACTS[$k]['work_history']);
                        $CONTACTS[$k]['work_history'] = array_unique($CONTACTS[$k]['work_history']);
                    }
                    if ($CONTACTS[$k]['education'] != '' and !is_array($CONTACTS[$k]['education'])) {
                        $CONTACTS[$k]['education'] = explode(",", $CONTACTS[$k]['education']);
                        $CONTACTS[$k]['education'] = array_unique($CONTACTS[$k]['education']);
                    }

                    if ($CONTACTS[$k]['photo'] != "") $CONTACTS[$k]['photo'] = base64_encode($CONTACTS[$k]['photo']);
                    $CONTACTS_TO_SAVE[$k]["work_history"] = $CONTACTS[$k]["work_history"];
                    $CONTACTS_TO_SAVE[$k]["title"] = $CONTACTS[$k]["title"];
                    $CONTACTS_TO_SAVE[$k]["company_name"] = $CONTACTS[$k]["company_name"];
                    $CONTACTS_TO_SAVE[$k]["location"] = $CONTACTS[$k]["location"];
                    $CONTACTS_TO_SAVE[$k]["member_id"] = $CONTACTS[$k]["member_id"];
                }

                if ($user->cache != '') {
                    $OLDCONTACTS = unserialize($user->cache);
                    $CONTACTS = array_merge($CONTACTS, $OLDCONTACTS);
                } else {

                }

                $user->cache = serialize($CONTACTS);
                $user->save();

                //}

                //if($user->cache!='') $CONTACTS = unserialize($user->cache);

                $_list = Lists::where('user_id', '=', Auth::user()->id)->first();

                $_list = unserialize($_list->cache);

                $linkeinURL = array();
                $linkeinID = array();

                for ($t = 0; $t < count($_list); $t++) {
                    $linkeinURL[$t] = $_list[$t]["linkedin"];
                }

                for ($k = 0; $k < count($CONTACTS); $k++) {

                    // Log::notice($CONTACTS[$k]["photo"]);

                    if ($CONTACTS[$k]["photo"] != '') $CONTACTS[$k]["photo"] = '<a href="' . base64_decode($CONTACTS[$k]["linkedin"]) . '" target="_blank"><img class="linkedin" src="' . base64_decode($CONTACTS[$k]["photo"]) . '"/></a>';

                    $linkedin_png = '<a href="' . base64_decode($CONTACTS[$k]["linkedin"]) . '" target="_blank"><img src="/resources/images/icons/linkedin_table.png"/></a>';
                    $CONTACTS[$k]['firstname'] = $CONTACTS[$k]['firstname'] . ' ' . $CONTACTS[$k]['lastname'] . '<br>' . $linkedin_png;

                    if (is_array($CONTACTS[$k]['work_history']) and count($CONTACTS[$k]['work_history']) > 0) {
                        $work_history_html = "<ul>";
                        foreach ($CONTACTS[$k]['work_history'] as $work_history_item) {
                            $work_history_html .= "<li>" . $work_history_item . "</li>";
                        }
                        $work_history_html .= "</ul>";
                        $CONTACTS[$k]['work_history'] = $work_history_html;
                    }


                    if (is_array($CONTACTS[$k]['education']) and count($CONTACTS[$k]['education']) > 0) {
                        $work_history_html2 = "<ul>";
                        foreach ($CONTACTS[$k]['education'] as $work_history_item2) {
                            $work_history_html2 .= "<li style='margin-left:20px;'>" . $work_history_item2 . "</li>";
                        }
                        $work_history_html2 .= "</ul>";
                        $CONTACTS[$k]['education'] = $work_history_html2;
                    }

                    if (in_array($CONTACTS[$k]['linkedin'], $linkeinURL)) {

                        $item_id = 0;
                        for ($t = 0; $t < count($_list); $t++) {

                            if ($CONTACTS[$k]['linkedin'] == $_list[$t]["linkedin"]) {
                                $item_id = $_list[$t]["action"];
                                $CONTACTS[$k]['action'] = '<div class="added_btn"><a class="btn btn-primary btn-lg" title="Added"><span class="added">Added</span><span class="view">View</span></a></div><input type="hidden" name="api_contact_id" value="' . $item_id . '" />';
                                $CONTACTS[$k]['action_id'] = $item_id;
                                break;
                            }

                        }

                    } else
                        $CONTACTS[$k]['action'] = '<div class="add_btn"><a id="item_' . $k . '" class="btn btn-primary btn-lg" title="Add" data-toggle="popover" data-placement="right">Add</a></div><input type="hidden" name="api_contact_id" value="' . $k . '" />';
                    $CONTACTS[$k]['action_id'] = $k;

                }
            } else {
                //added by developer for displaying add lead contact via chrome ext.								   
                if ($user->cache != '') $CONTACTS = unserialize($user->cache);
                $_list = Lists::where('user_id', '=', Auth::user()->id)->first();
                $_list = unserialize($_list->cache);
                $linkeinURL = array();
                $linkeinID = array();
                for ($t = 0; $t < count($_list); $t++) {
                    $linkeinURL[$t] = $_list[$t]["linkedin"];
                }
                for ($k = 0; $k < count($CONTACTS); $k++) {

                    if (!isset($CONTACTS[$k]["summary"])) $CONTACTS[$k]["summary"] = '';

                    if ($CONTACTS[$k]["photo"] != '') $CONTACTS[$k]["photo"] = '<a href="' . base64_decode($CONTACTS[$k]["linkedin"]) . '" target="_blank"><img class="linkedin" src="' . base64_decode($CONTACTS[$k]["photo"]) . '"/></a>';
                    $linkedin_png = '<a href="' . base64_decode($CONTACTS[$k]["linkedin"]) . '" target="_blank"><img src="/resources/images/icons/linkedin_table.png"/></a>';
                    $CONTACTS[$k]['firstname'] = $CONTACTS[$k]['firstname'] . ' ' . $CONTACTS[$k]['lastname'] . '<br>' . $linkedin_png;
                    /*if($CONTACTS[$k]['work_history'] != '' and !is_array($CONTACTS[$k]['work_history'])){
											$CONTACTS[$k]['work_history'] = explode(",", $CONTACTS[$k]['work_history']);
										  }*/
                    if (is_array($CONTACTS[$k]['work_history'])) {
                        $work_history_html = "<ul>";
                        foreach ($CONTACTS[$k]['work_history'] as $work_history_item) {
                            $work_history_html .= "<li>" . $work_history_item . "</li>";
                        }
                        $work_history_html .= "</ul>";
                        $CONTACTS[$k]['work_history'] = $work_history_html;
                    }
                    if (in_array($CONTACTS[$k]['linkedin'], $linkeinURL)) {
                        $item_id = 0;
                        for ($t = 0; $t < count($_list); $t++) {
                            if ($CONTACTS[$k]['linkedin'] == $_list[$t]["linkedin"]) {
                                $item_id = $_list[$t]["action"];
                                $CONTACTS[$k]['action'] = '<div class="added_btn"><a class="btn btn-primary btn-lg" title="Added"><span class="added">Added</span><span class="view">View</span></a></div><input type="hidden" name="api_contact_id" value="' . $item_id . '" />';
                                $CONTACTS[$k]['action_id'] = $item_id;
                                break;
                            }
                        }
                    } else
                        $CONTACTS[$k]['action'] = '<div class="add_btn"><a id="item_' . $k . '" class="btn btn-primary btn-lg" title="Add" data-toggle="popover" data-placement="right">Add</a></div><input type="hidden" name="api_contact_id" value="' . $k . '" />';
                    $CONTACTS[$k]['action_id'] = $k;
                }
            }


            $_JSON['iTotalRecords'] = count($CONTACTS);
            $_JSON['iTotalDisplayRecords'] = count($CONTACTS);

            $_CONTACTS = array();
            $sort_dir = SORT_DESC;

            if (count($CONTACTS) > 0) {

                if (Input::get('sSortDir_0') == 'desc') $sort_dir = SORT_DESC;
                if (Input::get('sSortDir_0') == 'asc') $sort_dir = SORT_ASC;

                if (Input::get('iSortCol_0') > 1) $CONTACTS = $this->array_orderby($CONTACTS, Input::get('mDataProp_' . $iSortCol), $sort_dir);


                $l = 0;

                for ($k = Input::get('iDisplayStart'); $k < (Input::get('iDisplayStart') + Input::get('iDisplayLength')); $k++) {

                    if ($k < count($CONTACTS)) {

                        $CONTACTS[$k]["linkedin"] = base64_decode($CONTACTS[$k]["linkedin"]);
                        $_CONTACTS[$l] = $CONTACTS[$k];
                        $l++;

                    }

                }

            }
            $this->saveLinkedinContact($CONTACTS_TO_SAVE);
            $_JSON['aaData'] = $_CONTACTS;
            //}
            /*catch (Exception $e) {
                   $_JSON['error']   = $e->getMessage()." , ".$e->getLine();
                   $_JSON['code']    = '0002';
            }*/

        } else {
            $_JSON['error'] = '';
            $_JSON['code'] = '0001';
        }

        //print_r($_JSON);die;

        return Response::json($_JSON);

    }

    function purchaseAllContact($index = null)
    {

        $_JSON = array();
        $_JSON['error'] = '';
        $_JSON['code'] = '';
        $_JSON['credits'] = 0;
        $_JSON['contacts'] = 0;

        if (Auth::check()) {

            $credits = Auth::user()->credits;
            $subscriptions = Auth::user()->subscription()->first();

            if ($subscriptions != null) {

                $transactions = $subscriptions->transaction()->get();

                if (count($transactions) > 0) {

                    foreach ($transactions as $transaction) {

                        $credits = $credits + $transaction->credits;

                    }

                }
            }

            $_JSON['error'] = '';

            $user = User::find(Auth::user()->id);

            $customername = $user->firstname . " " . $user->lastname;

            if ($credits == 0) {
                $_JSON['card'] = '';
                $_JSON['last4'] = '';
                $payment_profile = PaymentProfile::whereUser_id($user->id)->first();

                if (!empty($payment_profile)) {
                    $responseData = unserialize($payment_profile->response);
                    $responseData = $responseData->__toArray();
                    $cards = $responseData['cards']->__toArray();

                    if ($cards['total_count'] > 0) {
                        $card = $cards['data'][0]->__toArray();
                        $_JSON['card'] = $card['brand'];
                        $_JSON['last4'] = $card['last4'];
                    }
                }
                $_JSON['error'] = 'You don&#39;t have sufficient funds to purchase contacts';
            } else {
                $CONTACTS = unserialize($user->cache);

                $_JSON['info1'] = $CONTACTS[$index]["linkedin"];

                $agents = User::where("status", "=", User::STATUS_AGENT)->get();

                $min = 1000000;
                $min_id = NULL;

                foreach ($agents as $agent) {

                    $linkedin_list = LinkedinContact::where("agent_id", "=", $agent->id)->where("status", "=", LinkedinContact::INCOMPLETE)->get();
                    $count = 0;
                    foreach ($linkedin_list as $linkedin_item) {
                        $count++;
                    }

                    if ($count < $min) {
                        $min = $count;
                        $min_id = $agent->id;
                    }

                }

                $linkedin_contact = LinkedinContact::where("url", "=", base64_decode($CONTACTS[$index]["linkedin"]))->first();

                if (!empty($min_id)) {
                    $linkedin_contact->agent_id = $min_id;
                    $linkedin_contact->started = 1;
                    $linkedin_contact->save();
                }


                $company_domain_list = $this->searchCompany($CONTACTS[$index]['company_name']);

                if (is_array($company_domain_list)) {

                    foreach ($company_domain_list as $company_domain_item) {

                        try {

                            if ($company_domain_item['url'] != '') {
                                $company_domain = new CompanyDomain();
                                $company_domain->url = $company_domain_item['url'];
                                $company_domain->linkedin_url = $company_domain_item['linkedin_url'];
                                $company_domain = $linkedin_contact->companyDomain()->save($company_domain);
                                $linkedin_contact->save();

                            }

                        } catch (Exception $e) {
                            $_JSON['company_domain_list'] = $e->getMessage();
                        }

                    }

                }

                $_JSON['info2'] = $linkedin_contact;

                if ($linkedin_contact != null) {
                    $_JSON['info3'] = "saveToList";


                    $this->saveToList(Auth::user()->id, serialize($CONTACTS[$index]), $linkedin_contact->id);
                    $_JSON['credits'] = $credits - 1;

                    //send email to all addresses that are added from admin										
                    $contact_data = array("firstname" => $linkedin_contact->firstname, "lastname" => $linkedin_contact->lastname, "title" => $linkedin_contact->title, "company" => $linkedin_contact->company, "location" => $linkedin_contact->location, "url" => $linkedin_contact->url);

                    $agent_alerts = Settings::all();

                    foreach ($agent_alerts as $alert) {

                        $contact_data['customername'] = $customername;
                        $contact_data['agentname'] = $alert['name'];
                        $contact_data['agentemail'] = $alert['value'];
                        Session::put('agentemail', $alert['value']);

                        Mail::queue('emails/agent/agent-added-contact', $contact_data, function ($message) {
                            $message->to(Session::get('agentemail'), 'abc')->subject('A new lead has been added by a user.');
                        });
                    }
                    // send to admin
                    $contact_data['customername'] = $customername;
                    $contact_data['agentname'] = 'Admin';
                    $contact_data['agentemail'] = 'brandon.bornancin@gmail.com';
                    Mail::queue('emails/agent/admin-added-contact', $contact_data, function ($message) {
                        $message->to('signups@abcs.com', 'abc')->subject('A new lead has been added by a user.');
                    });

                }
            }

            $_JSON['contacts'] = count($user->lists()->first()->linkedinContact()->get());

        } else {
            $_JSON['error'] = '';
            $_JSON['code'] = '0001';
        }

        if ($index == 5)
            Log::notice("error");
        else
            Log::notice("json.response");

        if ($index == 5)
            return $_JSON["error"];
        else
            return $_JSON;
    }

    //purchase contact found on google
    //the main API used to purchase contact is data.com API
    function purchaseContact($_index = null, $_cache = null)
    {

        $_JSON = array();
        $_JSON['error'] = '';
        $_JSON['code'] = '';
        $_JSON['credits'] = 0;
        $_JSON['contacts'] = 0;

        if (Auth::check()) {

            $credits = Auth::user()->credits;
            $subscriptions = Auth::user()->subscription()->first();
            if ($subscriptions != null) {
                $transactions = $subscriptions->transaction()->get();
                if (count($transactions) > 0) {
                    foreach ($transactions as $transaction) {
                        $credits = $credits + $transaction->credits;
                    }
                }
            }

            $_JSON['error'] = '';

            $user = User::find(Auth::user()->id);

            $customername = $user->firstname . " " . $user->lastname;

            if ($credits == 0) {
                $_JSON['card'] = '';
                $_JSON['last4'] = '';
                $payment_profile = PaymentProfile::whereUser_id($user->id)->first();

                if (!empty($payment_profile)) {
                    $responseData = unserialize($payment_profile->response);
                    $responseData = $responseData->__toArray();
                    $cards = $responseData['cards']->__toArray();

                    if ($cards['total_count'] > 0) {
                        $card = $cards['data'][0]->__toArray();
                        $_JSON['card'] = $card['brand'];
                        $_JSON['last4'] = $card['last4'];
                    }
                }
                $_JSON['error'] = 'You don&#39;t have sufficient funds to purchase contacts';
            } else {
                $login_user_id = Auth::id();
                $index = Input::get('index', 0);
                if ($_cache == null)
                    $CONTACTS = unserialize($user->cache);
                else
                    $CONTACTS = $_cache;
                $_JSON['info1'] = $CONTACTS[$index]["linkedin"];
                $agents = User::where("status", "=", User::STATUS_AGENT)->get();
                $min = 1000000;
                $min_id = NULL;

                foreach ($agents as $agent) {
                    $linkedin_list = LinkedinContact::where("agent_id", "=", $agent->id)->where("status", "=", LinkedinContact::INCOMPLETE)->get();
                    $count = 0;
                    foreach ($linkedin_list as $linkedin_item) {
                        $count++;
                    }

                    if ($count < $min) {
                        $min = $count;
                        $min_id = $agent->id;
                    }
                }

                $linkedin_contact = LinkedinContact::where("url", "=", base64_decode($CONTACTS[$index]["linkedin"]))->first();


                if (!empty($min_id)) {
                    $linkedin_contact->agent_id = $min_id;
                    $linkedin_contact->started = 1;
                    $linkedin_contact->save();
                }


                // Automation Start
                $company_name = $CONTACTS[$index]['company_name'];
                $company_domain = '';
                if ($company_name != '') {
                    $obj = new GoogleCustomSearch();
                    $company_domain = $obj->makeCall($company_name); //google Search Domain
                }

                if ($company_domain != '') {
                    $this->addNewLead($company_domain, $linkedin_contact->id);
                }

                // Automation End

                $_JSON['info2'] = $linkedin_contact;
                if ($linkedin_contact != null) {
                    $_JSON['info3'] = "saveToList";


                    $this->saveToList(Auth::user()->id, serialize($CONTACTS[$index]), $linkedin_contact->id);
                    $_JSON['credits'] = $credits - 1;

                    //send email to all addresses that are added from admin										
                    $contact_data = array("firstname" => $linkedin_contact->firstname, "lastname" => $linkedin_contact->lastname, "title" => $linkedin_contact->title, "company" => $linkedin_contact->company, "location" => $linkedin_contact->location, "url" => $linkedin_contact->url);

                    $agent_alerts = Settings::all();

                    foreach ($agent_alerts as $alert) {

                        $contact_data['customername'] = $customername;
                        $contact_data['agentname'] = $alert['name'];
                        $contact_data['agentemail'] = $alert['value'];
                        Session::put('agentemail', $alert['value']);

                        Mail::queue('emails/agent/agent-added-contact', $contact_data, function ($message) {
                            $message->to(Session::get('agentemail'), 'abc')->subject('A new lead has been added by a user.');
                        });
                    }
                    // send to admin
                    $contact_data['customername'] = $customername;
                    $contact_data['agentname'] = 'Admin';
                    $contact_data['agentemail'] = 'brandon.bornancin@gmail.com';
                    Mail::queue('emails/agent/admin-added-contact', $contact_data, function ($message) {
                        $message->to('signups@abcs.com', 'abc')->subject('A new lead has been added by a user.');
                    });
                }
            }

            $_JSON['contacts'] = count($user->lists()->first()->linkedinContact()->get());
        } else {
            $_JSON['error'] = '';
            $_JSON['code'] = '0001';
        }

        if ($_index == 5)
            Log::notice("error");
        else
            Log::notice("json.response");

        if ($_index == 5)
            return $_JSON["error"];
        else
            return Response::json($_JSON);
    }

    function scrapeCompanySEO($company_web)
    {
        $seo_company_title = '';
        $seo_company_descriptoin = '';
        $seo_company_keywords = '';
        $url_html = $this->file_get_contents_curl($company_web);
        if ($url_html != '') {
            //parsing begins here:
            $doc = new DOMDocument();
            @$doc->loadHTML($url_html);
            $nodes = $doc->getElementsByTagName('title');

            //scrape page and get title,description and keywords:
            $seo_company_title = $nodes->item(0)->nodeValue;

            if ($seo_company_title != 'ERROR: The requested URL could not be retrieved') {
                $metas = $doc->getElementsByTagName('meta');
                for ($i = 0; $i < $metas->length; $i++) {
                    $meta = $metas->item($i);
                    if ($meta->getAttribute('name') == 'description' || $meta->getAttribute('name') == 'Description')
                        $seo_company_descriptoin = $meta->getAttribute('content');
                    if ($meta->getAttribute('name') == 'keywords' || $meta->getAttribute('name') == 'Keywords')
                        $seo_company_keywords = $meta->getAttribute('content');
                }
            }
        }

        return array('seo_company_title' => $seo_company_title, 'seo_company_descriptoin' => $seo_company_descriptoin, 'seo_company_keywords' => $seo_company_keywords);


    }

    function companySocialURL($domain)
    {

        $company_facebook_url = '';
        $company_twitter_url = '';
        $company_google_url = '';
        $full_contact_company = '';

        $apiKey = "somekey";
        $fullContact_Company_API = "https://api.fullcontact.com/v2/company/lookup.json?domain=$domain&apiKey=" . $apiKey;
        $ch = curl_init($fullContact_Company_API);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'caseysoftware/fullcontact-php-0.9.0');
        $response_json = curl_exec($ch);
        $full_contact_company = $response_json;
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $company_detail = json_decode($response_json);
        curl_close($ch);
        if (is_object($company_detail)) {
            if ($company_detail->status == 200) {
                if (isset($company_detail->socialProfiles)) {
                    $company_social_profiles = $company_detail->socialProfiles;
                    foreach ($company_social_profiles as $company_social_profile) {
                        if ($company_social_profile->typeId == 'facebook') {
                            $company_facebook_url = $company_social_profile->url;
                        } elseif ($company_social_profile->typeId == 'twitter') {
                            $company_twitter_url = $company_social_profile->url;
                        } elseif ($company_social_profile->typeId == 'google') {
                            $company_google_url = $company_social_profile->url;
                        }
                    }
                }
            }
        }

        return array('company_facebook_url' => $company_facebook_url, 'company_twitter_url' => $company_twitter_url, 'company_google_url' => $company_google_url, 'full_contact_company' => $full_contact_company);
    }

    function jsonContacts($_index = null, $_cache = null)
    {

        $_JSON = array();
        $_QUERY = array();
        $contacts = array();

        $_JSON['error'] = '';
        $_JSON['code'] = '';
        $config = Config::get('API');

        if (Auth::check()) {

            $credits = Auth::user()->credits;
            $subscriptions = Auth::user()->subscription()->first();

            if ($subscriptions != null) {

                $transactions = $subscriptions->transaction()->get();

                if (count($transactions) > 0) {

                    foreach ($transactions as $transaction) {

                        $credits = $credits + $transaction->credits;

                    }

                }
            }

            $_JSON['error'] = '';

            if ($credits == 0) $_JSON['error'] = 'You don&#39;t have sufficient funds to purchase contacts';
            else {

                $index = Input::get('index', 0);

                $user = User::find(Auth::user()->id);

                if ($_cache == null) $CONTACTS = unserialize($user->cache);
                else $CONTACTS = $_cache;


                if (count($CONTACTS) > 0) {

                    $FIELD = $CONTACTS[$index];

                    $www = explode(" ", trim($FIELD['www']));

                    for ($i = 0; $i < count($www); $i++) {

                        $www[$i] = explode('www.', $www[$i]);

                        if (!empty($www[$i][1])) $www[$i] = $www[$i][1];
                        else $www[$i] = $www[$i][0];

                    }

                    $www = array_unique($www);

                    /*-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

                    $first_last = "&firstname=" . urlencode($FIELD['name']) . "&lastname=" . urlencode($FIELD['lastname']);
                    $last_first = "&firstname=" . urlencode($FIELD['lastname']) . "&lastname=" . urlencode($FIELD['name']);

                    $_QUERY[0] = $first_last . "&name=" . urlencode($FIELD['company_name']);
                    $_QUERY[1] = $first_last . "&name=";

                    /*-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
                    $url = $config['api_jigsaw']['url'] . "/rest/searchContact.json?token=" . $config['api_jigsaw']['token'];
                    $i = 0;
                    /*----------------------------------------------------------------------------*/

                    $www_crawled = $this->searchCompany($FIELD['company_name']);

                    if (!empty($www_crawled)) {

                        foreach ($www_crawled as $key => $w) {
                            $www[] = $key;
                        }

                    }

                    $_www = array();

                    for ($w = 0; $w < count($www); $w++) {

                        if (!empty($www[$w])) {
                            if (!empty($www_crawled[$www[$w]])) {
                                $_www[] = $www[$w];
                            }
                        }
                    }

                    $www = $_www;

                    try {
                        if (count($contacts) == 0) {

                            if ($config['api_jigsaw']['status'] == true) {
                                $param = "&offset=0&pageSize=400" . $_QUERY[$i];
                                $json = file_get_contents($url . $param);
                                $decode = json_decode($json);
                                $contacts = $decode->contacts;
                                $this->saveContacts($contacts, $www[0], $www_crawled[$www[0]], serialize($www_crawled));
                            }

                            $CONTACTS[$index]['www'] = $www[0];
                            $CONTACTS[$index]['company_linkedin_www'] = $www_crawled[$www[0]];
                            $CONTACTS[$index]['www_variants'] = serialize($www_crawled);

                        }
                    } catch (Exception $e) {
                        Log::notice($e->getMessage());
                    }

                    $WWW = "";
                    $ino = 0;

                    $www = array_unique($www);

                    for ($w = 0; $w < count($www); $w++) {

                        if (!empty($www[$w])) {

                            $temp = explode('/', $www[$w]);
                            if (!empty($temp[1])) $www[$w] = $temp[0];

                        }

                    }

                    //Log::notice($www);

                    $CONTACTS[$index]['variants'] = implode(' ', $www);

                    /*----------------------------------------------------------------------------*/

                    $i = 1;

                    $_www = array();

                    for ($w = 0; $w < count($www); $w++) {
                        if (!empty($www[$w])) {
                            if (!empty($www_crawled[$www[$w]])) {
                                $_www[] = $www[$w];
                            }
                        }
                    }

                    $www = $_www;
                    try {
                        for ($w = 0; $w < count($www); $w++) {

                            if (!empty($www[$w])) {

                                if ($www[$w] != "") {


                                    $www[$w] = explode('www.', $www[$w]);

                                    if (!empty($www[$w][1])) $www[$w] = $www[$w][1];
                                    else $www[$w] = $www[$w][0];

                                    if (!empty($www_crawled[$www[$w]])) {

                                        if ($config['api_jigsaw']['status'] == true) {

                                            $param = "&offset=0&pageSize=400" . $_QUERY[$i] . urlencode($www[$w]);
                                            $WWW = $www[$w];
                                            $json = file_get_contents($url . $param);
                                            $decode = json_decode($json);
                                            $contacts = $decode->contacts;
                                            $this->saveContacts($contacts, $www[$w], $www_crawled[$www[$w]], serialize($www_crawled));

                                        }

                                        $CONTACTS[$index]['www'] = $www[$w];
                                        $CONTACTS[$index]['company_linkedin_www'] = $www_crawled[$www[$w]];
                                        $CONTACTS[$index]['www_variants'] = serialize($www_crawled);

                                    }

                                }
                            }
                        }
                    } catch (Exception $e) {
                        Log::notice($e->getMessage());
                    }

                    $contacts = array();
                    $person_id = 1;
                    $contacts = Person::where('firstname', '=', trim($FIELD['name']))->where('lastname', '=', trim($FIELD['lastname']))->get();

                    /*----------------------------------------------------------------------------*/
                    $person_id = 1;
                    $percent_temp = 0;
                    $api_contact_id = 0;

                    foreach ($contacts as $contact) {

                        similar_text($contact->title, $FIELD['title'], $percent1);
                        similar_text($contact->company_name, $FIELD['company_name'], $percent2);

                        if ($percent1 > 40 && $percent2 > 40) {

                            $percent = ($percent1 + $percent2) / 2;

                            if ($percent > $percent_temp) {

                                $percent_temp = $percent;
                                $person_id = $contact->id;
                                $api_contact_id = $contact->api_contact_id;

                            }

                        }

                    }

                    if ($percent_temp == 0) {

                        foreach ($contacts as $contact) {

                            for ($w = 0; $w < count($www); $w++) {

                                if (!empty($www[$w])) {

                                    similar_text($contact->title, $FIELD['title'], $percent);

                                    if ($percent > 40) {

                                        $pattern = '/' . trim($www[$w]) . '/i';
                                        $status = preg_match($pattern, $contact->www);

                                        if ($status == 1) {

                                            if ($percent > $percent_temp) {

                                                $percent_temp = $percent;
                                                $person_id = $contact->id;
                                                $api_contact_id = $contact->api_contact_id;

                                            }

                                        }

                                    }

                                }

                            }

                        }

                    }

                    /*----------------------------------------------------------------------------*/

                    $CONTACTS[$index]['email'] = '';
                    $CONTACTS[$index]['phone'] = '';

                    $CONTACTS[$index]['name'] = str_replace('-', '', $CONTACTS[$index]['name']);
                    $CONTACTS[$index]['lastname'] = str_replace('-', '', $CONTACTS[$index]['lastname']);
                    $CONTACTS[$index]['name'] = str_replace('_', '', $CONTACTS[$index]['name']);
                    $CONTACTS[$index]['lastname'] = str_replace('_', '', $CONTACTS[$index]['lastname']);
                    $CONTACTS[$index]['name'] = str_replace('.', '', $CONTACTS[$index]['name']);
                    $CONTACTS[$index]['lastname'] = str_replace('.', '', $CONTACTS[$index]['lastname']);
                    $CONTACTS[$index]['name'] = str_replace(',', '', $CONTACTS[$index]['name']);
                    $CONTACTS[$index]['lastname'] = str_replace(',', '', $CONTACTS[$index]['lastname']);
                    $CONTACTS[$index]['name'] = str_replace('>', '', $CONTACTS[$index]['name']);
                    $CONTACTS[$index]['lastname'] = str_replace('>', '', $CONTACTS[$index]['lastname']);
                    $CONTACTS[$index]['name'] = str_replace('<', '', $CONTACTS[$index]['name']);
                    $CONTACTS[$index]['lastname'] = str_replace('<', '', $CONTACTS[$index]['lastname']);

                    try {
                        $item_id = null;
                        if ($person_id != 1) {

                            $cnt = Person::find($person_id);

                            if ($cnt->email == "") {

                                if ($config['api_jigsaw']['status'] == true) {

                                    $contact_details = $config['api_jigsaw']['url'] . '/rest/contacts/' . $api_contact_id . '.json?token=' . $config['api_jigsaw']['token'] . '&username=' . $config['api_jigsaw']['username'] . '&password=' . $config['api_jigsaw']['password'] . '&purchaseFlag=true';
                                    $json = file_get_contents($contact_details);
                                    $decode = json_decode($json);
                                    $details = $decode->contacts[0];
                                    $cnt->email = $details->email;
                                    $cnt->phone = $details->phone;

                                }

                            }

                            if ($cnt->email != '') {

                                $new_domen = explode('@', $cnt->email);

                                if (!empty($new_domen[1]))
                                    $CONTACTS[$index]['www'] = $new_domen[1];

                                $cnt->www = $new_domen[1];
                                $cnt->save();

                            }

                            $CONTACTS[$index]['email'] = $cnt->email;
                            $CONTACTS[$index]['phone'] = $cnt->phone;

                            $_JSON['firstname'] = $CONTACTS[$index]['name'];
                            $_JSON['lastname'] = $CONTACTS[$index]['lastname'];
                            $_JSON['title'] = $CONTACTS[$index]['title'];
                            $_JSON['companyName'] = $CONTACTS[$index]['company_name'];
                            $_JSON['location'] = $CONTACTS[$index]['location'];
                            $_JSON['member_id'] = $CONTACTS[$index]['member_id'];
                            $_JSON['email'] = $CONTACTS[$index]['email'];
                            $_JSON['phone'] = $CONTACTS[$index]['phone'];

                            if ($CONTACTS[$index]['email'] != '') {

                                $_array = array();
                                $_array['email'] = $CONTACTS[$index]['email'];
                                $_array['result'] = true;
                                $_array['exactity'] = 'exact';
                                $CONTACTS[$index]['email'] = array($_array);
                            }

                            $item_id = $this->saveToList(Auth::user()->id, serialize($CONTACTS[$index]), $person_id);

                        } else $item_id = $this->saveToList(Auth::user()->id, serialize($CONTACTS[$index]), $person_id);

                        if (!empty($item_id)) {


                        }

                    } catch (Exception $e) {
                        Log::notice($e->getMessage());
                        $_JSON['error'] = 'The server is busy now, try again later!';
                        $_JSON['code'] = '0003';
                    }
                }
            }
        } else {
            $_JSON['error'] = '';
            $_JSON['code'] = '0001';
        }
        return Response::json($_JSON);
    }

    function contactView()
    {

        Session::put("item_id", Input::get("index"));

    }

    //save/update purchased contact
    function saveContacts($contacts, $www, $company_linkedin_www, $www_variants)
    {

        if (Auth::check()) {

            foreach ($contacts as $contact) {

                $person = Person::where('api_contact_id', '=', $contact->contactId)->first();

                if ($person == null) $person = new Person();

                $person->api_source = 'jigsaw.com';
                $person->check_sum_md5 = '';
                $person->api_company_id = $contact->companyId;
                $person->api_contact_id = $contact->contactId;
                $person->title = $contact->title;
                $person->company_name = $contact->companyName;

                $person->www = $www;
                $person->company_linkedin_www = $company_linkedin_www;
                $person->www_variants = $www_variants;

                $person->updated_date = $contact->updatedDate;
                $person->graveyard_status = $contact->graveyardStatus;
                $person->firstname = $contact->firstname;
                $person->lastname = $contact->lastname;
                $person->city = $contact->city;
                $person->state = $contact->state;
                $person->country = $contact->country;
                $person->zip = $contact->zip;
                $person->contact_url = $contact->contactURL;
                $person->seo_contact_url = $contact->seoContactURL;
                $person->area_code = $contact->areaCode;
                $person->address = $contact->address;
                $person->owned = $contact->owned;

                if ($person == null) {

                    $person->api_contact_sales = $contact->contactSales;
                    $person->owned_type = $contact->ownedType;
                    $person->phone = $contact->phone;
                    $person->email = $contact->email;

                }
                $person->save();
            }

        }

    }

    //save/update purchased contact
    function saveLinkedinContact($contacts)
    {
        foreach ($contacts as $contact) {
            $linkedin_contact = LinkedinContact::where('url', '=', $contact["linkedin"])->first();

            if ($contact["member_id"] != null && $contact["member_id"] != '' && $contact["member_id"] != 0) {
                if ($linkedin_contact == null)
                    $linkedin_contact = LinkedinContact::where('member_id', '=', $contact["member_id"])->first();
            }

            if ($linkedin_contact == null) {
                $linkedin_contact = new LinkedinContact();

            }

            if ($contact["member_id"] == 0) {
                $linkedin_contact->member_id = null;
            } else {
                $linkedin_contact->member_id = $contact["member_id"];
            }

            $linkedin_contact->photo = $contact["photo"];
            $linkedin_contact->url = $contact["linkedin"];
            $linkedin_contact->firstname = $contact["firstname"];
            $linkedin_contact->lastname = $contact["lastname"];
            $linkedin_contact->title = $contact["title"];
            $linkedin_contact->company = $contact["company_name"];
            $linkedin_contact->location = $contact["location"];
            $linkedin_contact->education = $contact["education"];
            $linkedin_contact->previous = $contact["previous"];
            if (isset($contact["is_scrape"])) {
                $linkedin_contact->is_scrape = $contact["is_scrape"];
            }
            if (isset($contact['linkedin_industry'])) {
                $linkedin_contact->linkedin_industry = $contact['linkedin_industry'];
            }

            if (isset($contact['linkedin_fullName'])) {
                $linkedin_contact->linkedin_fullName = $contact['linkedin_fullName'];
            }
            if (isset($contact['linkedin_position'])) {
                $linkedin_contact->linkedin_current = $contact['linkedin_position'];
                $linkedin_contact->linkedin_position = $contact['linkedin_position'];
            }
            if (isset($contact['linkedin_location'])) {
                $linkedin_contact->linkedin_location = $contact['linkedin_location'];
            }

            $linkedin_contact->save();
            $linkedin_contact = LinkedinContact::where('url', '=', $contact["linkedin"])->first();
            if ($contact["member_id"] != null and $contact["member_id"] != '') {
                if ($linkedin_contact == null)
                    $linkedin_contact = LinkedinContact::where('member_id', '=', $contact["member_id"])->first();
            }
            if (is_array($contact["work_history"])) {
                foreach ($contact["work_history"] as $work_history_item) {
                    try {
                        $work_history = new WorkHistory();
                        $work_history->title = $work_history_item;
                        $work_history = $linkedin_contact->workHistory()->save($work_history);
                        $linkedin_contact->save();
                    } catch (Exception $e) {

                    }
                }
            }
        }
    }

    //search contacts on google
    function addLead()
    {
        $_JSON['code'] = '';
        $_JSON['error'] = '';
        $_JSON['alert'] = 'test';

        $CONTACTS = array();
        $CONTACTS_TO_SAVE = array();
        $contacts = array();
        $_contact = array();
        $_CONTACT = array();
        $k = 0;
        if (Auth::check()) {

            try {

                $login_user_id = Auth::id();
                // if($login_user_id == 148){
                $public_url = FALSE;
                if (isset($_REQUEST['advance_serach']) && Input::get('advance_serach') == 1) {

                    $config = Config::get('API');
                    $input = '';
                    $input = str_replace(' ', '+', trim(Input::get('fullname')));
                    if (Input::get('title') != '') {
                        $input .= '+' . str_replace(' ', '+', trim(Input::get('title')));
                    }
                    if (Input::get('company_name') != '') {
                        $input .= '+at+' . str_replace(' ', '+', trim(Input::get('company_name')));
                    }
                    // echo $input.'<br>';

                    $url = $config['api_google']['url'] . "/cse?";
                    $url = $url . "cx=" . $config['api_google']['token'];//005430263908391132603:_vta5wxoapo
                    $url = $url . "&client=google-csbe";
                    $url = $url . "&output=xml_no_dtd";
                    $url = $url . "&lr=lang_en";
                    $url = $url . "&hl=en";
                    $url = $url . "&start=0&num=10";
                    $url = $url . "&as_qdr=all";
                    $url = $url . "&gl=uk+OR+us";
                    $url = $url . "&q=" . $input;

                    $google = file_get_contents($url);
                    $google = simplexml_load_string($google);


                    $find_linkedin = 'linkedin.com';
                    $find_last_name = strtolower(Input::get('lastname'));

                    if (count($google->RES->R) > 0) {
                        $linkedin_public_profile = '';
                        foreach ($google->RES->R as $R) {
                            $linkedin_public_profile = str_replace("'", "&#39;", $R->U);
                            $pos1 = strpos($linkedin_public_profile, $find_linkedin);
                            $pos2 = strpos($linkedin_public_profile, $find_last_name);
                            if ($pos1 !== false && $pos2 !== false) {
                                $public_url = TRUE;
                                break;
                            }
                        }
                    }
                }
                //}

                $user = User::find(Auth::user()->id);
                if (Input::get('linkedin_url', '') != '') {
                    //------------------------------------------------------------------------------                        

                    $_contact['action'] = '';
                    $_contact['action_id'] = '';
                    $_contact['photo'] = trim(Input::get('photo'));
                    $_contact['firstname'] = preg_replace('/\s+/', ' ', trim(strip_tags(trim(Input::get('firstname')))));
                    $_contact['name'] = preg_replace('/\s+/', ' ', trim(strip_tags(trim(Input::get('fullname')))));
                    $_contact['lastname'] = preg_replace('/\s+/', ' ', trim(strip_tags(trim(Input::get('lastname')))));
                    $_contact['title'] = preg_replace('/\s+/', ' ', trim(strip_tags(trim(Input::get('title')))));
                    $_contact['company_name'] = preg_replace('/\s+/', ' ', trim(strip_tags(trim(Input::get('company_name')))));
                    $_contact['company_url'] = preg_replace('/\s+/', ' ', trim(strip_tags(trim(Input::get('company_url')))));
                    $_contact['location'] = preg_replace('/\s+/', ' ', trim(strip_tags(trim(Input::get('location')))));
                    $_contact['education'] = preg_replace('/\s+/', ' ', trim(strip_tags(trim(Input::get('education')))));
                    $_contact['previous'] = preg_replace('/\s+/', ' ', trim(strip_tags(trim(Input::get('previous')))));
                    $_contact['is_scrape'] = Input::get('is_scrape');
                    $_contact['work_history'] = '';
                    $_contact['summary'] = '';


                    $_contact['linkedin_fullName'] = preg_replace('/\s+/', ' ', trim(strip_tags(trim(Input::get('fullname')))));
                    $_contact['linkedin_industry'] = preg_replace('/\s+/', ' ', trim(strip_tags(trim(Input::get('industry')))));
                    $_contact['linkedin_position'] = preg_replace('/\s+/', ' ', trim(strip_tags(trim(Input::get('title')))));
                    $_contact['linkedin_location'] = preg_replace('/\s+/', ' ', trim(strip_tags(trim(Input::get('location')))));

                    if (Input::get('summary', '') != '') {
                        $_contact['summary'] = preg_replace('/\s+/', ' ', trim(strip_tags(trim(Input::get('summary')))));
                    } else if (Input::get('description', '') != '') {
                        $_contact['summary'] = preg_replace('/\s+/', ' ', trim(strip_tags(trim(Input::get('description')))));
                    }

                    $work_history = explode("separator_p", trim(Input::get('work_history')));

                    if (count($work_history) - 1 != 0) {

                        $_contact['work_history'] = array();

                        for ($i = 0; $i < count($work_history) - 1; $i++)
                            $_contact['work_history'][$i] = $work_history[$i];

                    }


                    $_contact['www'] = '';
                    $_contact['company_linkedin_www'] = '';
                    $_contact['www_variants'] = '';
                    $_contact['variants'] = '';

                    if ($public_url == TRUE) {
                        $_contact['linkedin'] = $linkedin_public_profile;
                    } else {
                        $_contact['linkedin'] = trim(Input::get('linkedin_url'));
                    }
                    $_contact['member_id'] = trim(Input::get('member_id', NULL));
                    $_contact['industry'] = '';

                    $CONTACTS = array_add($CONTACTS, 0, $_contact);

                    $CONTACTS_TO_SAVE = array_add($CONTACTS_TO_SAVE, 0, $_contact);
                    //Log::notice($_contact);
                    //------------------------------------------------------------------------------

                    //$CONTACTS = $this->crawlLinkedin($CONTACTS);
                    //Log::notice($CONTACTS);

                    if ($CONTACTS[0]['photo'] != "") $CONTACTS[0]['photo'] = base64_encode($CONTACTS[0]['photo']);
                    $CONTACTS[0]['linkedin'] = base64_encode($CONTACTS[0]['linkedin']);
                    $user->cache = serialize($CONTACTS);
                    $user->save();


                    if ($user->cache != '') $CONTACTS = unserialize($user->cache);

                    $_list = Lists::where('user_id', '=', Auth::user()->id)->first();

                    $_list = unserialize($_list->cache);

                    $linkeinURL = array();
                    $linkeinID = array();

                    for ($t = 0; $t < count($_list); $t++) {

                        $linkeinURL[$t] = $_list[$t]["linkedin"];

                    }

                    for ($k = 0; $k < count($CONTACTS); $k++) {

                        // Log::notice($CONTACTS[$k]["photo"]);

                        if ($CONTACTS[$k]["photo"] != '') $CONTACTS[$k]["photo"] = '<a href="' . base64_decode($CONTACTS[$k]["linkedin"]) . '" target="_blank"><img class="linkedin" src="' . base64_decode($CONTACTS[$k]["photo"]) . '"/></a>';

                        $linkedin_png = '<a href="' . base64_decode($CONTACTS[$k]["linkedin"]) . '" target="_blank"><img src="/resources/images/icons/linkedin_table.png"/></a>';

                        $CONTACTS[$k]['firstname'] = $CONTACTS[$k]['firstname'] . ' ' . $CONTACTS[$k]['lastname'] . '<br>' . $linkedin_png;

                        if (is_array($CONTACTS[$k]['work_history'])) {

                            $work_history_html = "<ul>";
                            foreach ($CONTACTS[$k]['work_history'] as $work_history_item) {
                                $work_history_html .= "<li>" . $work_history_item . "</li>";
                            }
                            $work_history_html .= "</ul>";

                            $CONTACTS[$k]['work_history'] = $work_history_html;

                        }

                        if (in_array($CONTACTS[$k]['linkedin'], $linkeinURL)) {

                            $item_id = 0;
                            for ($t = 0; $t < count($_list); $t++) {

                                if ($CONTACTS[$k]['linkedin'] == $_list[$t]["linkedin"]) {
                                    $item_id = $_list[$t]["action"];
                                    $CONTACTS[$k]['action'] = '<div class="added_btn"><a class="btn btn-primary btn-lg" title="Added"><span class="added">Added</span><span class="view">View</span></a></div><input type="hidden" name="api_contact_id" value="' . $item_id . '" />';
                                    $CONTACTS[$k]['action_id'] = $item_id;
                                    break;
                                }

                            }

                        } else
                            $CONTACTS[$k]['action'] = '<div class="add_btn"><a class="btn btn-primary btn-lg" title="Add">Add</a></div><input type="hidden" name="api_contact_id" value="' . $k . '" />';
                        $CONTACTS[$k]['action_id'] = $k;
                    }

                    $this->saveLinkedinContact($CONTACTS_TO_SAVE);
                }
            } catch (Exception $e) {
                $_JSON['error'] = $e->getMessage();
                Log::notice("error = " . $e->getMessage() . " :: line=" . $e->getLine());
                $_JSON['code'] = '0002';
            }

        } else {
            $_JSON['error'] = '';
            $_JSON['code'] = '0001';
        }

        return Response::json($_JSON);

    }

    //save purchased contact to DB and credits consuming
    function saveToList($id, $contact, $linkedin_contact_id)
    {

        $item_id = null;

        if (Auth::check()) {

            $user = User::find(Auth::user()->id);

            if ($user->credits > 0) {

                $user->credits = $user->credits - 1;
                $user->save();

            } else {

                $subscription = Auth::user()->subscription()->first();
                $transactions = Auth::user()->subscription()->first()->transaction()->get();
                $plan_id = $subscription->plan_id;
                $plan = Plan::find($plan_id);

                foreach ($transactions as $transaction) {

                    if ($transaction->transaction_type == "charge" && $transaction->paid == 1 && $transaction->refunded == 0) {

                        if ($transaction->credits > 0) {

                            $transaction->credits = $transaction->credits - 1;
                            $transaction->save();
                            break;

                        }

                    }

                }

            }

            $list = Lists::where('user_id', '=', $id)->first();

            if (is_numeric($list->id)) {

                DB::table('list_item')->insert(array('lists_id' => $list->id, 'linkedin_contact_id' => $linkedin_contact_id, 'linkedin' => $contact));

                $lists = DB::table('list_item')->where('lists_id', '=', $list->id)->get();

                $item_id = $lists[count($lists) - 1]->id;

                DashboardController::cacheList($id, 0);

            }

        }

        return $item_id;
    }

    public function emailGenerator($firstname = null, $lastname = null, $domain = null)
    {

        $patterns = array();
        $email = array();
        $linkedin = array('firstname' => $firstname, 'lastname' => $lastname);

        if (!empty($domain)) {

            $_domain = explode("www.", $domain);

            if (count($_domain) == 2)
                $domain = $_domain[1];

            $patterns[0] = $linkedin['firstname'] . "." . $linkedin['lastname'];                      //first.last
            $patterns[1] = substr($linkedin['firstname'], 0, 1) . '' . $linkedin['lastname'];           //flast
            $patterns[2] = $linkedin['lastname'];                                                 //last
            $patterns[3] = $linkedin['firstname'] . "_" . $linkedin['lastname'];                      //first_last
            $patterns[4] = $linkedin['firstname'];                                                //first
            $patterns[5] = $linkedin['firstname'] . substr($linkedin['lastname'], 0, 1);              //firstl
            $patterns[6] = $linkedin['firstname'] . $linkedin['lastname'];                          //firstlast
            $patterns[7] = $linkedin['lastname'] . substr($linkedin['firstname'], 0, 1);              //lastf
            $patterns[8] = substr($linkedin['firstname'], 0, 1) . '.' . $linkedin['lastname'];          //f.last
            $patterns[9] = $linkedin['lastname'] . '.' . substr($linkedin['firstname'], 0, 1);         //last.f

            $email[""] = "select email";

            for ($i = 0; $i < 10; $i++) {
                $email[strtolower($patterns[$i] . '@' . $domain)] = strtolower($patterns[$i] . '@' . $domain);
            }
        }

        return $email;
    }

    //crawl contacts profiles from LinkedIn
    public function crawlLinkedinCronjob()
    {
        $URL = Input::get('url');
        $argv = "";
        $_PROFILES = array();
        $CONTACTS = array();

        $argv = $argv . str_replace("'", "&#39;", $URL) . " ";

        ob_start();
        $config = Config::get('crawler');
        //passthru($config['python'].' '.$config['script'].'/app/python/crawl_person_profile.py '.$argv);
        system($config['python'] . ' ' . $config['script'] . '/app/python/crawl_person_profile.py ' . $argv);
        $_PROFILES_STRING = ob_get_clean();
        print_r($_PROFILES_STRING);
        die;
        $_PROFILES_ARRAY = explode("ABCDEFGH123456789HGFEDCBA", $_PROFILES_STRING);
        for ($ko = 0; $ko < count($_PROFILES_ARRAY); $ko++) {
            $PROFILE_RESULT = explode("HGFEDCBA123456789ABCDEFGH", $_PROFILES_ARRAY[$ko]);
            if (!empty($PROFILE_RESULT[1])) {
                if (is_numeric(trim($PROFILE_RESULT[1]))) {
                    $_PROFILES[trim($PROFILE_RESULT[1])] = $PROFILE_RESULT[0];
                }
            } else {
                echo "empty";
            }
        }
        print_r($_PROFILES);
        die;
        /*if($_PROFILES[0] != ''){
				$profilehtml = new LinkedinProfileHtml();
				$profilehtml->profile = $_PROFILES[0];
				$profilehtml->save();
			}*/
    }

    public function addNewLead($domain, $linkedin_contact_id)
    {
        $domain = DomainHelper::getDomain($domain);

        $company_domain = CompanyDomain::where("linkedin_contact_id", "=", $linkedin_contact_id)->get();

        for ($i = 0; $i < count($company_domain); $i++) {
            $company_domain[$i]->status = CompanyDomain::UNSELECTED;
            $company_domain[$i]->save();
        }

        $company_domain = CompanyDomain::where("linkedin_contact_id", "=", $linkedin_contact_id)->where("url", "=", $domain)->first();

        if (count($company_domain) == 0) {
            $linkedin_contact = LinkedinContact::find($linkedin_contact_id);
            $company_domain = new CompanyDomain();
            $company_domain->url = $domain;
            $company_domain->linkedin_url = "";
            $company_domain->status = CompanyDomain::SELECTED;
            $company_domain = $linkedin_contact->companyDomain()->save($company_domain);
            $company_domain->save();

            if ($linkedin_contact->started_at == '' || $linkedin_contact->started_at == '0000-00-00 00:00:00') {
                $linkedin_contact->started_at = date("Y-m-d H:i:s");
                $linkedin_contact->save();
            }
        } else {

            $company_domain = CompanyDomain::find($company_domain->id);
            $company_domain->status = CompanyDomain::SELECTED;
            $company_domain->save();
        }


        //SEO Scrapping Start
        $company_seo_data = $this->scrapeCompanySEO($domain);
        $company_domain->seo_company_title = $company_seo_data['seo_company_title'];
        $company_domain->seo_company_descriptoin = $company_seo_data['seo_company_descriptoin'];
        $company_domain->seo_company_keywords = $company_seo_data['seo_company_keywords'];

        //SEO Scrapping End
        //Company Social URL --Start
        $company_social_url = $this->companySocialURL($domain);
        $company_domain->company_facebook_url = $company_social_url['company_facebook_url'];
        $company_domain->company_twitter_url = $company_social_url['company_twitter_url'];
        $company_domain->company_google_url = $company_social_url['company_google_url'];
        $company_domain->fullcontact_company = $company_social_url['full_contact_company'];
        //Company Social URL --End


        // Company Visistat --Start
        $params = array('website' => $domain); //Query by Domain
        $objVisistat = new Visistat();
        $response_Visistat = $objVisistat->makeCall($params);
        $company_domain->visistat = $response_Visistat;
        // Company Visistat URL --End

        $company_domain->save();

        $linkedin_contact = LinkedinContact::find($linkedin_contact_id);

        $domain_list = $linkedin_contact->companyDomain()->get();

        $domain = array();
        $email_list = array();
        $valid_email_array_list = array();
        $api_email_array_list = array();
        $emails = array();
        $key = "id-";

        foreach ($domain_list as $domain_item) {

            $domain_id = $domain_item->id;
            $domain["id-" . $domain_item->id] = $domain_item->url;

            if ($domain_item->status == CompanyDomain::SELECTED) {
                $key = "id-" . $domain_item->id;
                $email_list = $this->emailGenerator($linkedin_contact->firstname, $linkedin_contact->lastname, $domain_item->url);

                $final_domain = $domain_item->url;

                $test_domain = explode("www.", $domain_item->url);
                if (count($test_domain) == 2)
                    $final_domain = $test_domain[1];
                $check_email = strtolower($linkedin_contact->firstname . "_" . $linkedin_contact->lastname . '@' . $final_domain);

                //echo $check_email; exit;
                //save paid email for a specific contact according to history patterns of that domain
                $paid_db_email_patterns_list = PaidDatabaseEmail::select('url', 'pattern_id', 'level', 'percentage', 'paid_db')->distinct()->where("url", "=", $domain_item->url)->get();


                if (count($paid_db_email_patterns_list) > 0) {
                    foreach ($paid_db_email_patterns_list as $patterns) {
                        $paid_email = "";
                        switch ($patterns['pattern_id']) {
                            case 1:
                                $patt = $linkedin_contact->firstname;                                                      //first												
                                $paid_email = strtolower($patt . '@' . $final_domain);
                                break;
                            case 2:
                                $patt = $linkedin_contact->firstname . "." . $linkedin_contact->lastname;                      //first.last
                                $paid_email = strtolower($patt . '@' . $final_domain);
                                break;
                            case 3:
                                $patt = substr($linkedin_contact->firstname, 0, 1) . '' . $linkedin_contact->lastname;           //flast
                                $paid_email = strtolower($patt . '@' . $final_domain);
                                break;
                            case 4:
                                $patt = $linkedin_contact->lastname;                                                     //last
                                $paid_email = strtolower($patt . '@' . $final_domain);
                                break;
                            case 5:
                                $patt = $linkedin_contact->firstname . "_" . $linkedin_contact->lastname;                      //first_last
                                $paid_email = strtolower($patt . '@' . $final_domain);
                                break;
                            case 6:
                                $patt = $linkedin_contact->firstname . substr($linkedin_contact->lastname, 0, 1);              //firstl
                                $paid_email = strtolower($patt . '@' . $final_domain);
                                break;
                            case 7:
                                $patt = $linkedin_contact->firstname . $linkedin_contact->lastname;                          //firstlast
                                $paid_email = strtolower($patt . '@' . $final_domain);
                                break;
                            case 8:
                                $patt = $linkedin_contact->lastname . substr($linkedin_contact->firstname, 0, 1);              //lastf
                                $paid_email = strtolower($patt . '@' . $final_domain);
                                break;
                            case 9:
                                $patt = substr($linkedin_contact->lastname, 0, 1) . $linkedin_contact->firstname;              //lfirst
                                $paid_email = strtolower($patt . '@' . $final_domain);
                                break;
                            case 10:
                                $patt = substr($linkedin_contact->firstname, 0, 1) . '.' . $linkedin_contact->lastname;          //f.last
                                $paid_email = strtolower($patt . '@' . $final_domain);
                                break;
                            case 11:
                                $patt = $linkedin_contact->lastname . '.' . substr($linkedin_contact->firstname, 0, 1);         //last.f
                                $paid_email = strtolower($patt . '@' . $final_domain);
                                break;
                            case 12:
                                $patt = substr($linkedin_contact->firstname, 0, 1) . $linkedin_contact->lastname . substr(0, 1); //fl
                                $paid_email = strtolower($patt . '@' . $final_domain);
                                break;
                            case 13:
                                $patt = substr($linkedin_contact->firstname, 0, 1) . $linkedin_contact->lastname . "8";         //flast8
                                $paid_email = strtolower($patt . '@' . $final_domain);
                                break;
                            case 14:
                                $patt = $linkedin_contact->firstname . "-" . $linkedin_contact->lastname;                     //first-last
                                $paid_email = strtolower($patt . '@' . $final_domain);
                                break;
                            case 15:
                                $patt = $linkedin_contact->firstname . "." . substr($linkedin_contact->lastname, 0, 1);         //first.l
                                $paid_email = strtolower($patt . '@' . $final_domain);
                                break;
                            case 16:
                                $patt = $linkedin_contact->lastname . "." . $linkedin_contact->firstname;         //last.first
                                $paid_email = strtolower($patt . '@' . $final_domain);
                                break;
                            default:
                                //echo "default";
                        }

                        if ($paid_email != "") {
                            $_email_exist = PaidDatabaseEmail::where("email", "=", $paid_email)->get();
                            if (count($_email_exist) > 0) {
                                //$_JSON["result"] = "This mail is already taken";
                            } else {
                                try {
                                    $email_obj = new PaidDatabaseEmail();
                                    $email_obj->url = $patterns['url'];
                                    $email_obj->email = $paid_email;
                                    $email_obj->paid_db = $patterns['paid_db'];
                                    $email_obj->pattern_id = $patterns['pattern_id'];
                                    $email_obj->level = $patterns['level'];
                                    $email_obj->percentage = $patterns['percentage'];
                                    $email_obj->linkedin_contact_id = $linkedin_contact->id;
                                    $email_obj->save();
                                } catch (Exception $e) {
                                    Log::notice($e);
                                }
                            }
                        }
                    }
                }
                //saving paid db1 and paid db2 for final phone
                $paid_phone_list = PaidDatabasePhone::select('url', 'phone', 'level', 'percentage', 'paid_db')->distinct()->where("url", "=", $domain_item->url)->get();
                if (count($paid_phone_list) > 0) {
                    foreach ($paid_phone_list as $phone_item) {
                        $paid_phone = $phone_item['phone'];

                        if ($paid_phone != "") {
                            $_phone_exist = Phone::where("phone", "=", $paid_phone)->where("linkedin_contact_id", "=", $linkedin_contact->id)->get();
                            if (count($_phone_exist) > 0) {
                                //$_JSON["result"] = "This phone is already taken";
                            } else {
                                try {
                                    $ph_obj = new Phone();
                                    $ph_obj->phone = $paid_phone;
                                    $ph_obj->level = $phone_item['level'];
                                    $ph_obj->percentage = $phone_item['percentage'];
                                    $ph_obj->linkedin_contact_id = $linkedin_contact->id;
                                    $ph_obj->save();
                                    //$insertedId = $ph_obj->id;
                                } catch (Exception $e) {
                                    Log::notice($e);
                                }
                            }
                        }
                    }
                }


                // first name or last name has been changed

                $check_email_validator = EmailDomainValidator::where("domain_id", "=", $domain_id)->where('email_pattern', '=', $check_email)->orderBy('status', 'desc')->get()->toArray();
                $email_validator = EmailDomainValidator::where("domain_id", "=", $domain_id)->orderBy('status', 'desc')->get()->toArray();

                if (!count($email_validator) || !count($check_email_validator)) {
                    $emails = $email_list;
                    array_shift($emails);
                    set_time_limit(0);

                    foreach ($emails as $email) {

                        $api_key = '9bbfe3db-eeb8-413b-810c-f4aa02d62448';
                        $client = new BriteAPIContact($api_key);
                        $client->email = $email;
                        $client->verify();
                        $response = $client->response['email'];
                        $status = $response['status'];

                        $full_status = 0;
                        $likelihood = 0; //Added by developer

                        $validator = new EmailDomainValidator();
                        $validator->domain_id = $domain_id;
                        $validator->email_pattern = $email;
                        $validator->status = $response['status'];
                        $validator->disposable = $response['disposable'];
                        $validator->role_address = $response['role_address'];
                        $validator->duration = $response['duration'];
                        $validator->result = json_encode($response);
                        $validator->save();
                        $validator_id = $validator->id;

                        if ($response['status'] == 'invalid') {

                            // We are doing nothing with it. Domain might be Invalid

                            $response_json = "";
                            $errors = $client->errors();
                            $errors_codes = $client->error_codes();
                            $error_code = $errors_codes[0];
                            $error = $errors['email'];

                        } else {

                            // If status is not Valid, We are saving the pattern along with Full Contact Person API response

                            $valid_email_array_list[$email] = $email;
                            $response_json = $this->createWebhook($email, $validator_id);
                            $response_obj = json_decode($response_json);
                            $likelihood = 0;
                            $full_status = $response_obj->status;

                            // for api result copy patterns

                            if (is_object($response_obj)) {
                                if ($response_obj->status == 202) {
                                    //$api_email_list[$email->email_pattern] = $email->email_pattern;
                                    $api_email_array_list[$email] = $email;
                                } else if ($response_obj->status == 200) {
                                    //$api_email_list[$email->email_pattern] = $email->email_pattern;
                                    $api_email_array_list[$email] = $email;
                                    $likelihood = $response_obj->likelihood;
                                }
                            }

                            $error_code = "";
                            $error = "";
                        }

                        $validator->likelihood = $likelihood;
                        $validator->error_code = $error_code;
                        $validator->error = $error;

                        if ($response['status'] == 'valid') {

                            $objFullContactPerson = new FullContactPerson();
                            $response_json = $objFullContactPerson->makeCall($email);
                            $validator->fullcontact = $response_json;
                            $response_obj = json_decode($response_json);
                            $validator->full_status = $response_obj->status;

                        } else {

                            $validator->full_status = $full_status;
                            $validator->fullcontact = $response_json;

                        }

                        $validator->webhook = 1;
                        $validator->save();

                        // save record for accept all tracking
                        // This needs to be placed under BriteVerify Status Level based on its most VALID recommendation.

                        $accept_all_domain = CompanyDomain::find($domain_id);
                        $accept_all_domain->brite_api_status = $response['status'];
                        $accept_all_domain->save();

                        //auto save email for contact if it is valid by Brite api

                        /*

                        if ($response['status'] == 'valid') {
                            if (Email::where('email', '=', $email)->exists()) {
                                // email found
                            } else {
                                $email_object = new Email();
                                $email_object->linkedin_contact_id = $linkedin_contact->id;
                                $email_object->email = $email;
                                $email_object->level = 'Verified';
                                $email_object->percentage = '10';
                                $email_object->save();
                            }
                        }

                        */
                    }
                }


                // CONDITIONAL LOGIC FOR BRITEVERIFY STATUSES STARTS HERE

                // Initial Check - If any of the Email Pattern(s) is VALID

                $email_validator = EmailDomainValidator::where("domain_id", "=", $domain_id)->where('status', '=', "valid")->orderBy('status', 'desc')->get()->toArray();

                if (!empty($email_validator)) {

                    //echo " == Found Valid email ==" ;
                    //print_r ($email_validator);

                    $responseSocialURL = $this->socialURL($domain_id, 'valid');
                    $is_valid_lead = $responseSocialURL['is_valid'];

                    if ($is_valid_lead == 1) {

                        //echo " == Have found correct Social Data FullContact ==";
                    }

                    //else
                    //echo " == Not found correct Social Data in FullContact ==";
                    //exit ;

                    // Fetch FULL Contact Social information through Email validator table against found VALID records. And show the results in Email POP UP
                } elseif (empty($email_validator)) {
                    // Check if all email patterns are INVALID

                    $email_validator = EmailDomainValidator::where("domain_id", "=", $domain_id)->where('status', '=', "invalid")->orderBy('status', 'desc')->get()->toArray();

                    if (!empty($email_validator) && count($email_validator) == 10) {
                        //echo "== All Email Patterns are INVALID ==";

                        //print_r ($email_validator);
                        //exit;

                        // Domain might not correct or email patterns don't exist

                    } else {

                        // All are not Invalid, Check if we have got unknown or accept_all

                        $email_validator = EmailDomainValidator::whereIn('status', array('unknown', 'accept_all'))
                        ->where("domain_id", "=", $domain_id)
                        ->orderBy('status', 'desc')->get()->toArray();

                        if (!empty($email_validator)) {
                            // echo "== Found unknown and/or accept_all email ==" ;

                            // print_r ($email_validator);

                            // Validating Social Data

                            $responseSocialURL = $this->socialURL($domain_id, 'accept_all');
                            $is_valid_lead = $responseSocialURL['is_valid'];

                            if ($is_valid_lead == 1) {

                                //echo " == Have found correct Social Data in FullContact ==";

                                // WE NEED TO UPDATE THE EMAIL PATTERN STATUS to VALID

                            } elseif ($is_valid_lead == 0) {

                                // echo " == Not found any Social Data in FullContact ==";

                                // If Social Links are invalid then we would go further to following APis

                                //$objNetProspexPerson = new NetProspexPerson();
                                //$objZoomInfoPersonDetail = new ZoomInfoPersonDetail();

                                /*

                                // ZoominfoPersonMatch API
                                $objZoomInfoPersonMatch = new ZoomInfoPersonMatch();
                                $response_ZoomInfoPersonMatch = $objZoomInfoPersonMatch->makeCall(array('firstName' => $linkedin_contact->firstname,
                                                                                                        'lastName' => $linkedin_contact->lastname,
                                                                                                        'companyName' => $final_domain));

                                $company_domain = CompanyDomain::find($domain_id);
                                $company_domain->zoominfo_search = $response_ZoomInfoPersonMatch;
                                $company_domain->save();

                                */

                                /*

                                foreach ($email_validator as $key => $pattern) {

                                    // NetProsPexPerson API
                                    $response_NetProspexPerson = $objNetProspexPerson->makeCall(array('email' => $pattern['email_pattern'] ));

                                    // ZoominfoPersonDetail API
                                    $response_ZoomInfoPersonDetail = $objZoomInfoPersonDetail->makeCall(array('EmailAddress' => $pattern['email_pattern'] ));

                                    // Saving NetprosPex and Zoominfo API calls response in DB, email_validator table

                                    $validator = EmailDomainValidator::find($pattern['id']);

                                    $validator->netprospex_match = $response_NetProspexPerson;
                                    $validator->zoominfo_detail = $response_ZoomInfoPersonDetail;
                                    //$validator->zoominfo_detail = $response_ZoomInfoPersonMatch; // Saving Person Match Result in DB

                                    $validator->save();

                                }

                                */

                                // echo "== NetProsPex and ZoomInfo Response has been saved in DB ==" ;

                                // Fetch NetprosPex and Zoominfo Reponse to check if they have returned some Valid info

                                /*

                                $netProsPexResponse = $this->validateNetprosPex($domain_id, "accept_all") ;
                                $is_valid_netprospex = $netProsPexResponse['is_found'] ;

                                $zoomInfoResponse = $this->validateZoomInfo($domain_id, "accept_all") ;
                                $is_valid_zoominfo = $zoomInfoResponse['is_found'] ;

                                */

                                //if ( $is_valid_netprospex > 0  )
                                //{
                                // echo "== Found in NetprosPexPerson ==" ;

                                // WE NEED TO UPDATE THE EMAIL PATTERN STATUS to VALID

                                //} else {
                                // echo "== Not found in NetprosPexPerson ==";
                                //}

                                // LETS CHECK OUT ZOOMINFO RESPONSE

                                //if ( $is_valid_zoominfo > 0  )
                                //{
                                // echo "== Found in ZoominfoPerson ==" ;

                                // WE NEED TO UPDATE THE EMAIL PATTERN STATUS to VALID

                                //} else {
                                // echo "== Not found in ZoominfoPerson ==";
                                //}

                                // NetProsPex Person Search AND ZoomInfo Person Search

                                $is_valid_netprospex = 0;
                                $is_valid_zoominfo = 0;

                                if ($is_valid_netprospex == 0 && $is_valid_zoominfo == 0) {
                                    // We are unable to find success with FullcontactPerson, NetProsPexPerson and ZoominfoPerson, we need to keep looking

                                    // Before making following person search calls, we will look into company_domain_patterns table if we have already saved email patterns
                                    // for requested domain. If we already have, then we will get the patterns from DB and won't make new API calls for the same domain:

                                    $objNetProspexPersonList = new NetProspexPersonList();
                                    $objZoomInfoPersonSearch = new ZoomInfoPersonSearch();

                                    // NetProsPexPersonList API
                                    $response_NetProspexPersonList = $objNetProspexPersonList->makeCall(array('organizationWebsite' => $final_domain));

                                    // ZoominfoPersonDetail API
                                    $response_ZoomInfoPersonSearch = $objZoomInfoPersonSearch->makeCall(array('companyDomainName' => $final_domain, 'companyPastOrPresent' => '1'));


                                    // Saving NetprosPex and Zoominfo API calls response in DB, company_domain table

                                    if ($response_NetProspexPersonList != "") {

                                        $company_domain = CompanyDomain::find($domain_id);
                                        $company_domain->netprospex_list = $response_NetProspexPersonList;
                                        $company_domain->save();

                                        // Fetching email patterns / phone numbers out of Netprospex Person Search API to save these in
                                        // company_domain_patterns table for current / future use.

                                        $this->parseNetprospexList($domain_id, $final_domain);
                                    }

                                    if ($response_ZoomInfoPersonSearch != "") {

                                        $company_domain = CompanyDomain::find($domain_id);
                                        $company_domain->zoominfo_search = $response_ZoomInfoPersonSearch;
                                        $company_domain->save();

                                        // Fetching email patterns / phone numbers out of Zoominfo Person Search API to save these in
                                        // company_domain_patterns table for current / future use.

                                        $this->parseZoomInfoList($domain_id, $final_domain);
                                    }
                                }
                            }
                        }
                    }
                }

                // PHONE Validator Code goes here..

                $this->parsePhoneNumbers($domain_id);

                // Email pattern recognition Code
                // First we need to fetch the leads First Name, Last Name and Domain URL

                $response_lead = EmailDomainValidator::where("domain_id", "=", $domain_id)->get()->toArray();

                $leadEmails = array();

                foreach ($response_lead as $lead) {
                    $leadEmails[] = $lead['email_pattern'];
                }

                // Second, we need to fetch the domain email patterns through DB

                $response_domain = CompanyDomainPatterns::where("company_domain_id", "=", $domain_id)->get()->toArray();

                foreach ($response_domain as $lead) {
                    $array = $this->emailGenerator($lead['first_name'], $lead['last_name'], $final_domain);

                    $i = -1;

                    foreach ($array as $email) {
                        if ($email == $lead['email']) // If matching email pattern is found in lead patterns
                        {
                            // Update the lead email pattern status to 1 in company_status column to effect the Recommendation Formula on Email Popup

                            $validator = EmailDomainValidator::where('email_pattern', $leadEmails[$i])->first();
                            $validator->company_status = $validator->company_status + 1;
                            $validator->save();

                            break;
                        }

                        $i++;
                    }
                }

                // Preselect top email pattern as Current

                $this->markTopEmailAsCurrent($domain_id, 1);

                // exit ;

            }
        }
    }

    public function createWebhook($email, $validator_id)
    {
        //Log::notice('start Webhook');

        $dt = "http://login.abcs.com/agent/fullcontact-webhook";
        $url = "https://api.fullcontact.com/v2/person.json?email=" . $email . "&webhookUrl=" . $dt . "&webhookId=" . $validator_id . "&apiKey=somekey";


        // Open connection
        $ch = curl_init();
        // Set the url, number of GET vars, GET data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, false);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Execute request
        $result = curl_exec($ch);
        //$response_obj  = json_decode($result);
        // Close connection
        curl_close($ch);

        return $result;
        //Log::notice($result);
    }

    // This method is validating Social Data found against an Email Pattern within FullContactPerson API response

    public function socialURL($domain_id, $status)
    {
        $result = array();
        if ($status == "accept_all")
            $email_patterns = EmailDomainValidator::whereIn('status', array('unknown', 'accept_all'))
            ->where("domain_id", "=", $domain_id)
            ->orderBy('status', 'desc')->get()->toArray();
        else
            $email_patterns = EmailDomainValidator::where("domain_id", "=", $domain_id)->where("status", "=", $status)->orderBy('status', 'desc')->get()->toArray();

        if ($email_patterns != NULL) {
            $is_valid_flag = 0;
            foreach ($email_patterns as $key => $pattern) {
                $fullcontact_api = json_decode($pattern['fullcontact']);

                if (isset($fullcontact_api->status) && $fullcontact_api->status == 200) {

                    $result[$key]['email'] = $pattern['email_pattern'];

                    if (isset($fullcontact_api->socialProfiles) && $fullcontact_api->socialProfiles != NULL) {

                        $social_profiles = $fullcontact_api->socialProfiles;
                        $result[$key]['count'] = sizeof($social_profiles);
                        if ($result[$key]['count'] > 1) {
                            $is_valid_flag = 1;
                            //$result[$key]['is_valid'] = $is_valid_flag;
                        } elseif ($result[$key]['count'] == 1 && $is_valid_flag !== 1) {

                            foreach ($social_profiles as $social_profile) {

                                if ($social_profile->type != 'linkedin') {
                                    $is_valid_flag = 1;
                                } elseif ($social_profile->type == 'linkedin' && sizeof($email_patterns) == 1) {
                                    $is_valid_flag = 1;
                                }
                            }
                        }
                    }
                }

                // Updating social bit in email_validator table

                if (isset($is_valid_flag) && $is_valid_flag == 1) {

                    $validator = EmailDomainValidator::find($pattern['id']);
                    $validator->is_social = '1';
                    $validator->save();

                }

            }
        }

        return array('result' => $result, 'is_valid' => $is_valid_flag);
    }

    // This method is validating NetProsPexPerson API response. If it returns 0, no person found. If returns more than 0, person found.
    // Also update relative email pattern status to FOUND / 1 in email_validator table against netprospex status if it founds a lead

    public function validateNetprosPex($domain_id, $status)
    {
        $result = array();
        if ($status == "accept_all")
            $email_patterns = EmailDomainValidator::whereIn('status', array('unknown', 'accept_all'))
            ->where("domain_id", "=", $domain_id)
            ->orderBy('status', 'desc')->get()->toArray();
        else
            $email_patterns = EmailDomainValidator::where("domain_id", "=", $domain_id)->where("status", "=", $status)->orderBy('status', 'desc')->get()->toArray();

        if ($email_patterns != NULL) {
            $found = 0;
            foreach ($email_patterns as $key => $pattern) {

                $netprospex_match_api = json_decode($pattern['netprospex_match']);

                if (isset($netprospex_match_api->response->person_profile)) {
                    $found_count = $netprospex_match_api->response->person_profile->count;
                }

                // Validating the Email pattern based on Correct Finding in NetProsPex

                if (isset($found_count) && $found_count > 0) {

                    // Updating VALID status of found Email Pattern

                    $validator = EmailDomainValidator::find($pattern['id']);
                    $validator->net_status = '1';
                    $validator->save();

                    $found++;
                }
            }
        }

        return array('is_found' => $found);

    }

    // This method is validating ZoomInfoPerson API response. If it returns 0, no person found. If returns more than 0, person found.
    // Also update relative email pattern status to FOUND / 1 in email_validator table against zoomapi status if it founds a lead

    public function validateZoomInfo($domain_id, $status)
    {
        $result = array();
        if ($status == "accept_all")
            $email_patterns = EmailDomainValidator::whereIn('status', array('unknown', 'accept_all'))
            ->where("domain_id", "=", $domain_id)
            ->orderBy('status', 'desc')->get()->toArray();
        else
            $email_patterns = EmailDomainValidator::where("domain_id", "=", $domain_id)->where("status", "=", $status)->orderBy('status', 'desc')->get()->toArray();

        if ($email_patterns != NULL) {
            $found = 0;
            foreach ($email_patterns as $key => $pattern) {
                $zoominfo_detail = json_decode($pattern['zoominfo_detail']);
                if (isset($zoominfo_detail->PersonDetailRequest->ErrorMessage) && $zoominfo_detail->PersonDetailRequest->ErrorMessage == "No record found for the specified email address.")
                    $found_count = 0;
                elseif (isset($zoominfo_detail->PersonDetailRequest->PersonID) && $zoominfo_detail->PersonDetailRequest->PersonID != "") {

                    // Validating the Email pattern based on Correct Finding in Zoominfo
                    // Updating VALID status of found Email Pattern

                    $validator = EmailDomainValidator::find($pattern['id']);
                    $validator->zoom_status = '1';
                    $validator->save();

                    $found_count = 1;
                } else
                    $found_count = 0;

                if ($found_count > 0)
                    $found++;
            }
        }

        return array('is_found' => $found);

    }

    // Fetching email patterns / phone numbers out of Netprospex Person Search API to save these in
    // company_domain_patterns table for current / future use.

    public function parseNetprospexList($domain_id, $domain_url)
    {
        $result = array();

        $response = CompanyDomain::where("id", "=", $domain_id)->get()->toArray();
        $netprospex = $response[0]['netprospex_list'];

        if ($netprospex != NULL) {
            $netprospex_response = json_decode($netprospex);

            if (isset($netprospex_response->response->person_list)) {
                if ($netprospex_response->response->person_list->count > 0) {

                    foreach ($netprospex_response->response->person_list->persons as $person) {

                        // Save patterns in company_domain_patterns table

                        if (isset($person->email) && count($person->email) > 0) {

                            $pos = strpos($person->email, $domain_url);

                            if ($pos != false) {

                                $domain_pattern = new CompanyDomainPatterns();
                                $domain_pattern->company_domain_id = $domain_id;
                                $domain_pattern->url = $domain_url;
                                $domain_pattern->first_name = $person->firstName;
                                $domain_pattern->last_name = $person->lastName;
                                $domain_pattern->email = $person->email;
                                if (isset($person->organization->phones) && count($person->organization->phones) > 0) {
                                    $domain_pattern->phone = $person->organization->phones[0]->formattedNumber;
                                    $domain_pattern->phone_type = "company";
                                }
                                $domain_pattern->api_type = "netprospex";
                                $domain_pattern->save();
                            }
                        }
                    }
                }
            }
        }
    }

    // Fetching email patterns / phone numbers out of Zoominfo Person Search API to save these in
    // company_domain_patterns table for current / future use.

    public function parseZoomInfoList($domain_id, $domain_url)
    {
        $result = array();

        $response = CompanyDomain::where("id", "=", $domain_id)->get()->toArray();
        $zoominfo = $response[0]['zoominfo_search'];

        if ($zoominfo != NULL) {
            $zoominfo_response = json_decode($zoominfo);

            if (isset($zoominfo_response->PeopleSearchRequest->TotalResults)) {
                if ($zoominfo_response->PeopleSearchRequest->TotalResults > 0) {

                    foreach ($zoominfo_response->PeopleSearchRequest->PeopleSearchResults->PersonRecord as $person) {

                        // Save patterns in company_domain_patterns table

                        if (isset($person->Email) && count($person->Email) > 0) {

                            $pos = strpos($person->Email, $domain_url);

                            if ($pos != false) {

                                $domain_pattern = new CompanyDomainPatterns();
                                $domain_pattern->company_domain_id = $domain_id;
                                $domain_pattern->url = $domain_url;
                                $domain_pattern->first_name = $person->FirstName;
                                $domain_pattern->last_name = $person->LastName;
                                $domain_pattern->email = $person->Email;
                                if (isset($person->CurrentEmployment->Company->CompanyPhone) && $person->CurrentEmployment->Company->CompanyPhone != "") {
                                    $domain_pattern->phone = $person->CurrentEmployment->Company->CompanyPhone;
                                    $domain_pattern->phone_type = "company";
                                }
                                $domain_pattern->api_type = "zoominfo";
                                $domain_pattern->save();
                            }
                        }
                    }
                }
            }
        }
    }


    public function parsePhoneNumbers($domain_id)
    {
        $phone_number = array();
        $email_patterns = EmailDomainValidator::where("domain_id", "=", $domain_id)->orderBy('status', 'desc')->get()->toArray();
        foreach ($email_patterns as $email_pattern) {
            // Handle Netprospex Person Match API Call Response
            if ($email_pattern['net_status'] == 1) {
                $netprospex_details = json_decode($email_pattern['netprospex_match']);
                foreach ($netprospex_details as $netprospex_detail) {
                    $person = $netprospex_detail->person_profile->person;
                    if (isset($person->phones)) {       // Check Person Phone Numbers
                        $netprospex_user_phones = $person->phones;
                        foreach ($netprospex_user_phones as $netprospex_user_phone) {
                            if ($netprospex_user_phone->label == 'directPhone') {
                                $netprospex_user_phone->formattedNumber = $this->filterPhoneNumber($netprospex_user_phone->formattedNumber);
                                $phone_record = array('domain_id' => $domain_id, 'phone' => $netprospex_user_phone->formattedNumber, 'type' => 'direct', 'is_current' => 0);
                                array_push($phone_number, $phone_record);
                            }
                        }
                    }

                    if (isset($person->organization->phones)) { // Check Organization Phone Numbers
                        $netprospex_organization_phones = $person->organization->phones;
                        foreach ($netprospex_organization_phones as $netprospex_organization_phone) {
                            $netprospex_organization_phone->formattedNumber = $this->filterPhoneNumber($netprospex_organization_phone->formattedNumber);
                            $phone_record = array('domain_id' => $domain_id, 'phone' => $netprospex_organization_phone->formattedNumber, 'type' => 'company', 'is_current' => 0);
                            array_push($phone_number, $phone_record);
                        }
                    }
                }
            }

            // Handle Zoominfo Person Detail API Call Response
            if ($email_pattern['zoom_status'] == 1) {
                $zoominfo_details = json_decode($email_pattern['zoominfo_detail']);
                foreach ($zoominfo_details as $zoominfo_detail) {
                    if (isset($zoominfo_detail->DirectPhone)) {
                        $zoominfo_detail->DirectPhone = $this->filterPhoneNumber($zoominfo_detail->DirectPhone);
                        $phone_record = array('domain_id' => $domain_id, 'phone' => $zoominfo_detail->DirectPhone, 'type' => 'direct', 'is_current' => 0);
                        array_push($phone_number, $phone_record);
                        //echo 'Direct Phone :'.$zoominfo_detail->DirectPhone.'<br>';
                    }
                    if (isset($zoominfo_detail->CompanyPhone)) {
                        $zoominfo_detail->CompanyPhone = $this->filterPhoneNumber($zoominfo_detail->CompanyPhone);
                        $phone_record = array('domain_id' => $domain_id, 'phone' => $zoominfo_detail->CompanyPhone, 'type' => 'company', 'is_current' => 0);
                        array_push($phone_number, $phone_record);
                        // echo 'Company :'.$zoominfo_detail->CompanyPhone.'<br>';
                    }
                }
            }
        }
        $compnay_domain_detail = CompanyDomain::find($domain_id);  // Domain Data
        $full_contact_company_api_call = json_decode($compnay_domain_detail->fullcontact_company);    //Full Contact Comapny API Call
        $visistat_company_api_call = json_decode($compnay_domain_detail->visistat);   //Visistat API CALL for Website
        if ($full_contact_company_api_call != '' && $full_contact_company_api_call->status == 200) {
            $organizaiton_detail = $full_contact_company_api_call->organization;
            if (isset($organizaiton_detail->contactInfo->phoneNumbers)) {
                $phoneNumbers = $organizaiton_detail->contactInfo->phoneNumbers;
                foreach ($phoneNumbers as $phoneNumber) {
                    $phoneNumber->number = $this->filterPhoneNumber($phoneNumber->number);
                    $phone_record = array('domain_id' => $domain_id, 'phone' => $phoneNumber->number, 'type' => 'company', 'is_current' => 0);
                    array_push($phone_number, $phone_record);
                }
            }
        }

        if ($visistat_company_api_call != '' && $visistat_company_api_call->status == 'success') {
            $organizaiton_detail = $visistat_company_api_call->data;
            if (isset($organizaiton_detail[0]->phone) && $organizaiton_detail[0]->phone != '') {
                $organizaiton_detail[0]->phone = $this->filterPhoneNumber($organizaiton_detail[0]->phone);
                $phone_record = array('domain_id' => $domain_id, 'phone' => $organizaiton_detail[0]->phone, 'type' => 'company', 'is_current' => 0);
                array_push($phone_number, $phone_record);
            }
        }


        $is_current = FALSE;
        $unique_array = array();
        $final_array = array();

        if (sizeof($phone_number) == 1) {
            $phone_number[0]['is_current'] = 1;
            $final_array = $phone_number;
        } elseif (sizeof($phone_number) > 1) {
            $i = 0;
            foreach ($phone_number as $key => $phone) {
                if (!in_array($phone['phone'], $unique_array)) {
                    array_push($unique_array, $phone['phone']);
                    $final_array[$i] = $phone;
                    $final_array[$i]['count'] = 1;
                    if ($phone['type'] == 'direct' && $is_current == FALSE) {
                        $final_array[$i]['is_current'] = 1;
                        $is_current = TRUE;
                    }
                    $i = $i + 1;

                } else {
                    foreach ($final_array as $skey => $final_arr) {
                        if ($phone['phone'] == $final_arr['phone']) {
                            $final_array[$skey]['count'] = $final_array[$skey]['count'] + 1;
                            if ($final_arr['type'] == 'direct' && $is_current == FALSE) {
                                $final_array[$skey]['is_current'] = 1;
                                $is_current = TRUE;
                            }
                            break;
                        }
                    }
                }
            }
        }


        //return $final_array;
        if ($final_array != NULL) {
            PhoneValidator::where('domain_id', '=', $domain_id)->delete(); //Remove old phone number
            DB::table('phone_validator')->insert($final_array); //Insert New phone numbers

            if ($is_current == FALSE) {
                // mark current - Phone having highest count
                $highest_phone_count = PhoneValidator::where('domain_id', '=', $domain_id)->orderBy('count', 'desc')->first();
                $highest_phone_count->is_current = 1;
                $highest_phone_count->save();
            }

        }
    }

    public function filterPhoneNumber($phone_number)
    {
        $phone_number = ltrim($phone_number, '+');
        $phone_number = ltrim($phone_number, '(');
        $phone_number = str_replace(' ', '', $phone_number);
        $phone_number = str_replace('(', '.', $phone_number);
        $phone_number = str_replace(')', '.', $phone_number);
        $phone_number = str_replace('-', '.', $phone_number);
        $phone_number = str_replace('ext.', ' ext ', $phone_number);
        //echo 'Sub :'.substr($phone_number, 0 , 2).'<br>';
        if (substr($phone_number, 0, 2) != '1.') {
            $phone_number = '1.' . $phone_number;
        }
        return $phone_number;
    }

}