@if($row->filename)
    <a href="/seminar/{{$row->id}}/berkas" target="_blank">{{$row->filename}}</a>
@endif
