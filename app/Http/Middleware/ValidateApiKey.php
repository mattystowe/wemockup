<?php
//Validate the api key for applications.
//
//
//
//
//
//
namespace App\Http\Middleware;

use Closure;
use App\Application;
class ValidateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      $body = $request->all();

      //find application
      $app = Application::where('api_key',$request->api_key)->get();

      if ($app->isEmpty()) {
        return response('Invalid api token.', 401);
      } else {
        if ($app[0]->api_key != $request->api_key) {
          return response('Invalid api token.', 401);
        }
      }



    //pass the application through to the rest of the application
    //available in the request
    //
    $request->merge([
          'Application' => $app[0]
      ]);
    return $next($request);
    }



}
