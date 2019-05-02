<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NewOrderTest extends TestCase
{

  use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testShopifyWebHook()
    {
      $this->expectsJobs(App\Jobs\Emails\NewOrder::class);

      $payload = '{
                    "id": 123456,
                    "email": "matt.stowe@perazimgroup.com",
                    "closed_at": null,
                    "created_at": "2016-07-11T16:12:11-04:00",
                    "updated_at": "2016-07-11T16:12:11-04:00",
                    "number": 234,
                    "note": null,
                    "token": null,
                    "gateway": null,
                    "test": true,
                    "total_price": "403.00",
                    "subtotal_price": "393.00",
                    "total_weight": 0,
                    "total_tax": "0.00",
                    "taxes_included": false,
                    "currency": "USD",
                    "financial_status": "voided",
                    "confirmed": false,
                    "total_discounts": "5.00",
                    "total_line_items_price": "398.00",
                    "cart_token": null,
                    "buyer_accepts_marketing": true,
                    "name": "#9999",
                    "referring_site": null,
                    "landing_site": null,
                    "cancelled_at": "2016-07-11T16:12:11-04:00",
                    "cancel_reason": "customer",
                    "total_price_usd": null,
                    "checkout_token": null,
                    "reference": null,
                    "user_id": null,
                    "location_id": null,
                    "source_identifier": null,
                    "source_url": null,
                    "processed_at": null,
                    "device_id": null,
                    "browser_ip": null,
                    "landing_site_ref": null,
                    "order_number": 1234,
                    "discount_codes": [
                    ],
                    "note_attributes": [
                    ],
                    "payment_gateway_names": [
                      "visa",
                      "bogus"
                    ],
                    "processing_method": "",
                    "checkout_id": null,
                    "source_name": "web",
                    "fulfillment_status": "pending",
                    "tax_lines": [
                    ],
                    "tags": "",
                    "contact_email": "jon@doe.ca",
                    "order_status_url": null,
                    "line_items": [
                      {
                        "id": 808950810,
                        "variant_id": null,
                        "title": "Some Template",
                        "quantity": 1,
                        "price": "199.00",
                        "grams": 200,
                        "sku": "1",
                        "variant_title": null,
                        "vendor": null,
                        "fulfillment_service": "manual",
                        "product_id": 632910392,
                        "requires_shipping": true,
                        "taxable": true,
                        "gift_card": false,
                        "name": "IPod Nano - 8GB",
                        "variant_inventory_management": null,
                        "properties": [
                        ],
                        "product_exists": true,
                        "fulfillable_quantity": 1,
                        "total_discount": "0.00",
                        "fulfillment_status": null,
                        "tax_lines": [
                        ]
                      },
                      {
                        "id": 199,
                        "variant_id": null,
                        "title": "IPod Nano - 8GB",
                        "quantity": 1,
                        "price": "199.00",
                        "grams": 200,
                        "sku": "2",
                        "variant_title": null,
                        "vendor": null,
                        "fulfillment_service": "manual",
                        "product_id": 632910392,
                        "requires_shipping": true,
                        "taxable": true,
                        "gift_card": false,
                        "name": "IPod Nano - 8GB",
                        "variant_inventory_management": null,
                        "properties": [
                        ],
                        "product_exists": true,
                        "fulfillable_quantity": 1,
                        "total_discount": "5.00",
                        "fulfillment_status": null,
                        "tax_lines": [
                        ]
                      }
                    ],
                    "shipping_lines": [
                      {
                        "id": null,
                        "title": "Generic Shipping",
                        "price": "10.00",
                        "code": null,
                        "source": "shopify",
                        "phone": null,
                        "delivery_category": null,
                        "carrier_identifier": null,
                        "tax_lines": [
                        ]
                      }
                    ],
                    "billing_address": {
                      "first_name": "Bob",
                      "address1": "123 Billing Street",
                      "phone": "555-555-BILL",
                      "city": "Billtown",
                      "zip": "K2P0B0",
                      "province": "Kentucky",
                      "country": "United States",
                      "last_name": "Biller",
                      "address2": null,
                      "company": "My Company",
                      "latitude": null,
                      "longitude": null,
                      "name": "Bob Biller",
                      "country_code": "US",
                      "province_code": "KY"
                    },
                    "shipping_address": {
                      "first_name": "Steve",
                      "address1": "123 Shipping Street",
                      "phone": "555-555-SHIP",
                      "city": "Shippington",
                      "zip": "K2P0S0",
                      "province": "Kentucky",
                      "country": "United States",
                      "last_name": "Shipper",
                      "address2": null,
                      "company": "Shipping Company",
                      "latitude": null,
                      "longitude": null,
                      "name": "Steve Shipper",
                      "country_code": "US",
                      "province_code": "KY"
                    },
                    "fulfillments": [
                    ],
                    "refunds": [
                    ],
                    "customer": {
                      "id": null,
                      "email": "john@test.com",
                      "accepts_marketing": false,
                      "created_at": null,
                      "updated_at": null,
                      "first_name": "Matt",
                      "last_name": "Stowe",
                      "orders_count": 0,
                      "state": "disabled",
                      "total_spent": "0.00",
                      "last_order_id": null,
                      "note": null,
                      "verified_email": true,
                      "multipass_identifier": null,
                      "tax_exempt": false,
                      "tags": "",
                      "last_order_name": null,
                      "default_address": {
                        "id": null,
                        "first_name": null,
                        "last_name": null,
                        "company": null,
                        "address1": "123 Elm St.",
                        "address2": null,
                        "city": "Ottawa",
                        "province": "Ontario",
                        "country": "Canada",
                        "zip": "K2H7A8",
                        "phone": "123-123-1234",
                        "name": "",
                        "province_code": "ON",
                        "country_code": "CA",
                        "country_name": "Canada",
                        "default": true
                      }
                    }
                  }';
$payloadarray = json_decode($payload,TRUE);
      $this->json('POST', '/shopify/neworder/apikeygoeshere', $payloadarray)
           ->seeJson([
               'email' => 'matt.stowe@perazimgroup.com',
               'amount' => '398.00',
               'firstname' => 'Matt',
               'lastname' => 'Stowe'
           ]);
    }
}
