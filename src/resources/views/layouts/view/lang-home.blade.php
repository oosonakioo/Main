@foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
    <a type="button" class="btn btn-link" rel="alternate" hreflang="{{$localeCode}}"
       href="{{LaravelLocalization::getLocalizedURL($localeCode) }}"><img src="{{ asset('images/lan_'. $localeCode. '.png') }}"></a>
@endforeach
