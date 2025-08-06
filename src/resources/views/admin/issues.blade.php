@extends('layouts.admin')

@section('header', trans('admin.'. $menu))

@section('content')
    <div class="box box-solid">
        <div class="box-body">
            <table id="table-result" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="15%">{{ trans('admin.contact-date') }}</th>
                    <th width="20%">{{ trans('admin.categories-contact-issue-topic') }}</th>
                    <th>{{ trans('admin.contact-name') }}</th>
                    <th>{{ trans('admin.contact-tel') }}</th>
                    <th>{{ trans('admin.contact-email') }}</th>
                    <!-- <th width="7%">{{ trans('admin.read') }}</th> -->
                    <th width="7%">{{ trans('admin.contents-delete') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($issues as $issue)
                    <tr id="row-{{ $issue->id }}" class="issue-row" data-toggle="modal" data-target="#issue-modal"
                        data-id="{{ $issue->id }}"
                        data-name="{{ $issue->name }}"
                        @if ($issue->issue_topic_id == 0)
                          data-issuetopic="{{ trans('contact.other') }} ({{ $issue->issue }})"
                        @else
                          data-issuetopic="{{ $issue->issueTopic->title }}"
                        @endif
                        data-tel="{{ $issue->tel }}"
                        data-email="{{ $issue->email }}"
                        data-detail="{{ $issue->detail }}">
                        <td nowrap>
                          {{--*/
                            $eng_date=strtotime($issue->created_at);
                          /*--}}
                          <span class="hidden">{{ $issue->created_at }}</span>
                          {{ Helper::thai_date($eng_date, 'short') }}
                        </td>
                        @if ($issue->issue_topic_id == 0)
                          <td>{{ trans('contact.other') }}</td>
                        @else
                          <td>{{ $issue->issueTopic->title }}</td>
                        @endif
                        <td>{{ $issue->name }}</td>
                        <td>{{ $issue->tel }}</td>
                        <td>{{ $issue->email }}</td>
                        <!-- <td align="center">
                            @if($issue->read)
                                <span class="label label-success">{{ trans('admin.read') }}</span>
                            @else
                                <span class="label label-warning">{{ trans('admin.unread') }}</span>
                            @endif
                        </td> -->
                        <td align="center">
                            <a class="btn btn-sm btn-danger delete" href="#" data-id="{{ $issue->id }}">{{ trans('admin.contents-delete') }}</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="issue-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="issue-modal-label">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
              </div>
              <div class="modal-body">
                <strong class="modal-contact"></strong>
                <hr />
                <div class="modal-detail"></div>
              </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(function () {
            $('#table-result').DataTable()
                            .order([0, 'desc'])
                            .draw();

            $('#issue-modal').on('show.bs.modal', function (event) {
                var row = $(event.relatedTarget);
                //console.log(row.data('id'));
                var id = row.data('id');
                var name = row.data('name');
                var issueTopic = row.data('issuetopic');
                var tel = row.data('tel');
                var email = row.data('email');
                var detail = row.data('detail');

                var modal = $(this);
                modal.find('.modal-title').html("{{ trans('admin.categories-contact-issue-topic') }} : " + issueTopic);
                modal.find('.modal-contact').html("Name : " + name +"<br />Tel : " + tel + "<br />Email : " + email);
                modal.find('.modal-detail').html(detail);
            })

            @include('admin.view.view-delete-js', ['url' => 'admin/issues'])
        });
    </script>
@endsection
