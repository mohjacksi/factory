@can($viewGate)
    <a class="btn btn-xs btn-primary"
       href="{{ route('admin.' . $crudRoutePart . '.show',isset($parent)?[$parent,$row->id]: $row->id) }}">
        {{ trans('global.view') }}
    </a>
@endcan
@can($editGate)
    <a class="btn btn-xs btn-info"
       href="{{ route('admin.' . $crudRoutePart . '.edit', isset($parent)?[$parent,$row->id]: $row->id) }}">
        {{ trans('global.edit') }}
    </a>
@endcan
@can($deleteGate)
    <form action="{{ route('admin.' . $crudRoutePart . '.destroy', isset($parent)?[$parent,$row->id]: $row->id) }}"
          method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
    </form>
@endcan
@if(isset($excel) )
    @can($approveGate)
        <a class="btn btn-xs btn-info"
           href="{{ route('admin.' . $crudRoutePart . '.approve', isset($parent)?[$parent,$row->id]: $row->id) }}">
            {{ trans('global.approve') }}
        </a>
    @endcan
@endif
