<h3>
    Current Company: {{ $website->uuid }}
</h3>
Hostnames:
<ul>
    @foreach($website->hostnames as $host)
        {{ $host->fqdn }}
    @endforeach
</ul>