@if($row->filename)
    <a href="/webinar/{{$row->id}}/berkas" target="_blank">{{$row->filename}}</a>
@endif
