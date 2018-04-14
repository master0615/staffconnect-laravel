<?php
namespace App\Http\Controllers;

use Hyn\Tenancy\Contracts\Repositories\CustomerRepository;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Models\Customer;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Illuminate\Http\Request;

class ClientsController extends Controller
{

    public function confirmDeleteClient($id)
    {
        $w = Website::findOrFail($id);
        if ($w->customer) {
            $cname = $w->customer->name;
        } else {
            $cname = $w->uuid;
        }
        return view("deleteClient", [
            'id' => $id,
            'cname' => $cname,
        ]);
    }

    // delete websites and hostname, not customer. make sure tenancy config set to delete tenant folder and db
    public function deleteClient(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:websites',
        ]);

        $w = app(WebsiteRepository::class)->findById($request->id);
        foreach ($w->hostnames as $host) {
            app(HostnameRepository::class)->delete($host, 1);
        }
        app(WebsiteRepository::class)->delete($w, 1);

        return redirect('/clients');
    }

    public function getClients()
    {
        $websites = Website::all();

        foreach ($websites as $w) {
            $urls = [];
            foreach ($w->hostnames as $host) {
                $urls[] = $host->fqdn;
            }
            $w->urls = $urls;
            if ($w->customer) {
                $w->client = $w->customer->name;
            }
        }
        return view("clients", [
            'websites' => $websites,
        ]);
    }

    public function newClient(Request $request)
    {
        $request->validate([
            'cname' => 'required|unique:customers,name|min:1,max:30',
            'url' => 'required|min:2,max:30',
            'email' => 'required|email',
        ]);

        $customer = new Customer();
        $customer->name = $request->cname;
        $customer->email = $request->email;
        app(CustomerRepository::class)->create($customer);

        $website = new Website();
        $website->uuid = env('DB_DATABASE') . '_' . $request->url; // shorten if too long for db name?
        $website->customer_id = $customer->id;
        app(WebsiteRepository::class)->create($website);

        $hostname = new Hostname();
        $hostname->fqdn = $request->url . ".staffconnect.net";
        $hostname->customer_id = $customer->id;
        app(HostnameRepository::class)->create($hostname);
        app(HostnameRepository::class)->attach($hostname, $website);

        return redirect('/client/' . $website->id);
    }

    public function viewClient($id)
    {
        $w = Website::findOrFail($id);
        if ($w->customer) {
            $cname = $w->customer->name;
        } else {
            $cname = $w->uuid;
        }
        return view("client", [
            'id' => $id,
            'cname' => $cname,
        ]);
    }
}
