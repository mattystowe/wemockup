<?php
/**
 * Class to help the logging of objects so all order information is always present.
 *
 * eg statically
 *
 * MockupLogger::Order
 * MockupLogger::Item
 * MockupLogger::ItemJob
 *
 *
 *
 *
 *
 */

namespace App;

use Log;


class MockupLogger
{



    public static function Order($level, Order $order, $msg, $data = null) {
        $logdata = [
          'order_id'=>$order->id,
          'order_email'=>$order->email,
          'order_orderuid'=>$order->orderuid,
          'data'=>$data
        ];
        self::send($level, $msg, $logdata);
    }

    public static function Item($level, Item $item, $msg, $data = null) {
      $logdata = [
        'order_id'=>$item->order->id,
        'order_email'=>$item->order->email,
        'order_orderuid'=>$item->order->orderuid,
        'item_id'=>$item->id,
        'item_itemuid'=>$item->itemuid,
        'data'=>$data
      ];
      self::send($level, $msg, $logdata);
    }

    public static function ItemJob($level, ItemJob $itemjob, $msg, $data = null) {
      $logdata = [
        'order_id'=>$itemjob->item->order->id,
        'order_email'=>$itemjob->item->order->email,
        'order_orderuid'=>$itemjob->item->order->orderuid,
        'item_id'=>$itemjob->item->id,
        'item_itemuid'=>$itemjob->item->itemuid,
        'itemjob_id'=>$itemjob->id,
        'itemjob_frame'=>$itemjob->frame,
        'data'=>$data
      ];
      self::send($level, $msg, $logdata);
    }





    private static function send($level, $msg, $data = null) {
      switch ($level) {
        case 'debug':
          Log::debug($msg, $data);
          break;
          case 'info':
            Log::info($msg, $data);
            break;
            case 'warning':
              Log::warning($msg, $data);
              break;
              case 'error':
                Log::error($msg, $data);
                break;
      }
    }



}
