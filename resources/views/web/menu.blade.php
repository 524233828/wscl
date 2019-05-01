<div class="menu">
    @foreach($menus as $menu)
    <li @if($menu->link == url()->current()) class="select" @endif onclick="location.href = '{{$menu->link}}'">{{$menu->name}}</li>
    @endforeach
</div>