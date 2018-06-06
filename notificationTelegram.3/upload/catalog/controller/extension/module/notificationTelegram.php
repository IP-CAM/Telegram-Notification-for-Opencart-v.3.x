<?php
/**
 * Created by PhpStorm.
 * User: basheir
 * Date: 6 يون، 2018 م
 * Time: 1:57 ص
 */


class ControllerExtensionModuleNotificationTelegram extends Controller
{


    public function sendOrderAlert(&$route, &$data, &$output)
    {


        $this->load->model('setting/setting');
        $setting = $this->model_setting_setting->getSetting('notificationTelegram');

        if (isset($setting['notificationTelegram_order_alert'])) {


            //  load order data
            $this->load->model('account/order');
            $order_products = $this->model_account_order->getOrderProducts($data[0]);

            $message = "You have a new order\n";
            $message .= $this->buldArray($order_products[0]);
            $this->sendMessagetoTelegam($setting, $message);

        }


    }

    public function sendAccountAlert(&$data)
    {

        $this->load->model('setting/setting');
        $setting = $this->model_setting_setting->getSetting('notificationTelegram');

        if (isset($setting['notificationTelegram_order_alert'])) {

            $message = "New Customer";
            $this->sendMessagetoTelegam($setting, $message);


        }
    }


    //Send  message To notificationTelegram

    public function sendMessagetoTelegam($setting, $msg)
    {


        $this->load->model('setting/setting');
        $setting = $this->model_setting_setting->getSetting('notificationTelegram');

        //print_r($setting);
        $botToken = $setting['notificationTelegram_boot_token'];
        $website = "https://api.telegram.org/bot" . $botToken;
        $chatIds = $setting['notificationTelegram_chat_ids'];  //Receiver Chat Id

        if (is_array($chatIds)) {


            foreach ($chatIds as $val) {
                $this->initMessage($botToken, $val, $msg);
            }

        } else {
            $this->initMessage($botToken, $chatIds, $msg);

        }


    }


    private function initMessage($botToken, $chatID, $msg)
    {

        $website = "https://api.telegram.org/bot" . $botToken;

        $params = [
            'chat_id' => $chatID,
            'text' => $msg,
        ];
        $ch = curl_init($website . '/sendMessage');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);

    }


    public function buldArray($arr)
    {


        if (is_array($arr)) {
            $dataAttributes = array_map(function ($value, $key) {
                return "$key -->> $value  \n";
            }, array_values($arr), array_keys($arr));

            return $dataAttributes = implode(' ', $dataAttributes);


        }


    }


}