<div class="module-group">
    @foreach($children as $child)
        {!! $child->render() !!}
    @endforeach
</div>