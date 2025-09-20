<?php

class ControllerExtensionModuleBitrix24 extends Controller
{
    public function createLead($data)
    {
        $fields = ['TITLE' => $data['title']];

        if (isset($data['name'])) $fields['NAME'] = $data['name'];
        if (isset($data['last_name'])) $fields['LAST_NAME'] = $data['last_name'];
        if (isset($data['email'])) $fields['EMAIL'] = [['VALUE' => $data['email'], 'VALUE_TYPE' => 'WORK']];
        if (isset($data['phone'])) $fields['PHONE'] = [['VALUE' => $data['phone'], 'VALUE_TYPE' => 'WORK']];
        if (isset($data['price'])) $fields['OPPORTUNITY'] = $data['price'];
        if (isset($data['price'])) $fields['CURRENCY_ID'] = "BYN";
        if (isset($data['comments'])) $fields['COMMENTS'] = $data['comments'];

        $query_data = [
            'fields' => array_merge($fields, [
                'ASSIGNED_BY_ID' => 1,
                'SOURCE_ID' => 'WEB'
            ]),
            'params' => ['REGISTER_SONET_EVENT' => 'Y']
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => constant('B24_WEBHOOK_URL') . 'crm.lead.add.json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($query_data),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $b24_result = curl_exec($ch);
        $b24_result = json_decode($b24_result, true);
        curl_close($ch);

        return $b24_result;
    }

    public function createOrderLead($order_id)
    {
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);

        if ($order_info) {
            $order_products = $this->model_checkout_order->getOrderProducts($order_id);
            $order_products_line = "";
            $order_total = round(floatval($order_info['total']), 2);

            foreach ($order_products as $product) {
                $name = $product['name'];
                $quantity = $product['quantity'];
                $price = round(floatval($product['price']), 2);

                $order_products_line .= "$quantity x $name - $price BYN\n";
            }

            $order_details = "Список товаров:\n{$order_products_line}\nВсего: $order_total BYN\n{$order_info['payment_method']} / {$order_info['shipping_method']}";

            $this->createLead([
                'title' => "Заказ на сайте №$order_id",
                'name' => $order_info['firstname'],
                'last_name' => $order_info['lastname'],
                'email' => $order_info['email'],
                'phone' => $order_info['telephone'],
                'price' => $order_total,
                'comments' => $order_details,
            ]);
        }
    }
}
