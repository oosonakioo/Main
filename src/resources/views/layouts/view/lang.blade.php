@foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
    <a rel="alternate" hreflang="{{$localeCode}}"
       href="{{LaravelLocalization::getLocalizedURL($localeCode) }}">{{ $properties['native'] }}</a> &nbsp;
@endforeach