@extends('layouts.email')
@section('content')
<p>
  Hey {{$order->firstname}}!
</p>
<p>
  We noticed that you have not yet setup some of the items on your order below.  Please do reach out to us if you need any help.
</p>

<div>
    @foreach ($order->items as $item)
    <p>
      <span><img alt="{{$item->sku->product->name}}" align="middle" src="{{$item->sku->product->image}}" /></span>
      <span>{{$item->sku->product->name}}</span>
    </p>
    @endforeach
</div>

<div><!--[if mso]>
                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://" style="height:50px;v-text-anchor:middle;width:200px;" arcsize="8%" stroke="f" fillcolor="#178f8f">
                          <w:anchorlock/>
                          <center>
                        <![endif]-->
<a href="{{ config('app.url')}}/order#!/dashboard/{{$order->orderuid}}" style="background-color:#178f8f;border-radius:4px;color:#ffffff;display:inline-block;font-family:Helvetica, Arial, sans-serif;font-size:16px;font-weight:bold;line-height:50px;text-align:center;text-decoration:none;width:200px;-webkit-text-size-adjust:none;">Setup Order</a>
                        <!--[if mso]>
                          </center>
                        </v:roundrect>
                      <![endif]-->
</div>
@endsection
