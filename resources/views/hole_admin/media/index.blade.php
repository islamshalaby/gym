@extends('hole_admin.app')
@section('title' , __('messages.videos_images'))
@section('styles')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="/admin//admin/assets/css/forms/theme-checkbox-radio.css">
    <link href="/admin/plugins/lightbox/photoswipe.css" rel="stylesheet" type="text/css"/>
    <link href="/admin/plugins/lightbox/default-skin/default-skin.css" rel="stylesheet" type="text/css"/>
    <link href="/admin/plugins/lightbox/custom-photswipe.css" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL STYLES -->
@endsection
@section('content')
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h4>{{ __('messages.videos_images') }}</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-12">
                    <a class="btn btn-info" data-toggle="modal"
                       data-target="#add_new_Modal"> {{ __('messages.add_image') }} </a>
                    {{--                    <a class="btn btn-info" data-toggle="modal"--}}
                    {{--                       data-target="#add_new_video_Modal"> {{ __('messages.add_video') }} </a>--}}
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">

            <div class="card-body">
                <div class="row">
                    @foreach($data as $row)
                        @if($row->type == 'image')
                            <div class="row col-md-12">
                                <div class="col-md-6">
                                    <a  class="img-1"
                                       href="{{media_image_cloudinary_url()}}{{ $row->image }}"
                                       data-size="1600x1068"
                                       data-med="{{media_image_cloudinary_url()}}{{ $row->image }}"
                                       data-med-size="1024x683"
                                       data-author="Samuel Rohl">
                                        <img style="height: 250px; width: 400px;"
                                             src="{{media_image_cloudinary_url()}}{{ $row->image }}"
                                             alt="image-gallery">
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <form action="{{route('media.delete')}}" method="post">
                                        @csrf
                                        <input type="hidden" value="{{$row->id}}" id="txt_media_id" name="media_id">
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <button type="submit" class="btn btn-danger"
                                                title="{{ __('messages.delete') }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="row col-md-12">
                                <div class="col-md-6">
                                    <a id="22" data-media-id="{{$row->id}}" class="img-1"
                                       href="{{media_image_cloudinary_url()}}{{ $row->image }}"
                                       data-size="1600x1068"
                                       data-med="{{media_image_cloudinary_url()}}{{ $row->image }}"
                                       data-med-size="1024x683"
                                       data-author="Samuel Rohl">
                                        <video width="400" height="350" controls>
                                            <source
                                                src="https://res.cloudinary.com/dsibvtsiv/video/upload/v1621843606/{{ $row->image }}"
                                                type="video/mp4">
                                            <source src="movie.ogg" type="video/ogg">
                                            Your browser does not support the video tag.
                                        </video>
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <div class="avatar avatar-xl">
                                        <img class="rounded align-middle" src="{{media_image_cloudinary_url()}}{{ $row->thumbnail }}" />
                                    </div>
                                    
                                </div>
                                <div class="col-md-2">
                                    <form action="{{route('media.delete')}}" method="post">
                                        @csrf
                                        <input type="hidden" value="{{$row->id}}" id="txt_media_id" name="media_id">
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <br>
                                        <button type="submit" class="btn btn-danger"
                                                title="{{ __('messages.delete') }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                    @endif


                @endforeach
                {{--            <div id="demo-test-gallery container" class="demo-gallery" data-pswp-uid="1">--}}
                {{--                @foreach($data as $row)--}}
                {{--                    @if($row->type == 'image')--}}
                {{--                        <a id="btn_media" data-media-id="{{$row->id}}" class="img-1"--}}
                {{--                           href="{{media_image_cloudinary_url()}}{{ $row->image }}" data-size="1600x1068"--}}
                {{--                           data-med="{{media_image_cloudinary_url()}}{{ $row->image }}" data-med-size="1024x683"--}}
                {{--                           data-author="Samuel Rohl">--}}
                {{--                            <img style="height: 350px;" src="{{media_image_cloudinary_url()}}{{ $row->image }}"--}}
                {{--                                 alt="image-gallery">--}}
                {{--                        </a>--}}
                {{--                    @else--}}

                {{--                        <a id="22" data-media-id="{{$row->id}}" class="img-1"--}}
                {{--                           href="{{media_image_cloudinary_url()}}{{ $row->image }}" data-size="1600x1068"--}}
                {{--                           data-med="{{media_image_cloudinary_url()}}{{ $row->image }}" data-med-size="1024x683"--}}
                {{--                           data-author="Samuel Rohl">--}}
                {{--                            <video width="400" height="350" controls>--}}
                {{--                                <source--}}
                {{--                                    src="https://res.cloudinary.com/dsibvtsiv/video/upload/v1621843606/{{ $row->image }}"--}}
                {{--                                    type="video/mp4">--}}
                {{--                                <source src="movie.ogg" type="video/ogg">--}}
                {{--                                Your browser does not support the video tag.--}}
                {{--                            </video>--}}
                {{--                        </a>--}}

                {{--                    @endif--}}
                {{--                @endforeach--}}
                {{--            </div>--}}
                <!-- Root element of PhotoSwipe. Must have class pswp. -->
                    <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
                        <!-- Background of PhotoSwipe. It's a separate element, as animating opacity is faster than rgba(). -->
                        <div class="pswp__bg"></div>
                        <!-- Slides wrapper with overflow:hidden. -->
                        <div class="pswp__scroll-wrap">
                            <!-- Container that holds slides. PhotoSwipe keeps only 3 slides in DOM to save memory. -->
                            <!-- don't modify these 3 pswp__item elements, data is added later on. -->
                            <div class="pswp__container">
                                <div class="pswp__item"></div>
                                <div class="pswp__item"></div>
                                <div class="pswp__item"></div>
                            </div>
                            <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
                            <div class="pswp__ui pswp__ui--hidden">
                                <div class="pswp__top-bar">
                                    <!--  Controls are self-explanatory. Order can be changed. -->
                                    <div class="pswp__counter"></div>
                                    <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                                    <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                                    <form action="{{route('media.delete')}}" method="post">
                                        @csrf
                                        <input type="hidden" id="txt_media_id" name="media_id">
                                        <button type="submit" class="btn btn-danger"
                                                title="{{ __('messages.delete') }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    <!-- element will get class pswp__preloader--active when preloader is running -->
                                    <div class="pswp__preloader">
                                        <div class="pswp__preloader__icn">
                                            <div class="pswp__preloader__cut">
                                                <div class="pswp__preloader__donut"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                                    <div class="pswp__share-tooltip"></div>
                                </div>
                                <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
                                </button>
                                <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
                                </button>
                                <div class="pswp__caption">
                                    <div class="pswp__caption__center"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--    for store new media--}}
            <div id="add_new_Modal" class="modal animated zoomInUp custo-zoomInUp" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('messages.add_new_media') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                        <form action="{{route('media.store')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="custom-file-container" data-upload-id="myFirstImage">
                                    <label>{{ __('messages.upload') }} ({{ __('messages.multiple_images') }}) <a
                                            href="javascript:void(0)" class="custom-file-container__image-clear"
                                            title="Clear Image">x</a></label>
                                    <label class="custom-file-container__custom-file">
                                        <input type="file" required name="images[]" multiple
                                               class="custom-file-container__custom-file__custom-file-input"
                                               accept="image/*">
                                        <input type="hidden" name="MAX_FILE_SIZE" value="10485760"/>
                                        <span class="custom-file-container__custom-file__custom-file-control"></span>
                                    </label>
                                    <div class="custom-file-container__image-preview"></div>
                                </div>
                                <div class="custom-file-container" data-upload-id="mySecondImage">
                                    <label>{{ __('messages.upload') }} ({{ __('messages.video_thumbnail') }}) <a
                                            href="javascript:void(0)" class="custom-file-container__image-clear"
                                            title="Clear Image">x</a></label>
                                    <label class="custom-file-container__custom-file">
                                        <input type="file" required name="thumbnail[]" multiple
                                               class="custom-file-container__custom-file__custom-file-input"
                                               accept="image/*">
                                        <input type="hidden" name="MAX_FILE_SIZE" value="10485760"/>
                                        <span class="custom-file-container__custom-file__custom-file-control"></span>
                                    </label>
                                    <div class="custom-file-container__image-preview"></div>
                                </div>
                            </div>
                            
                            <div class="modal-footer">
                                <button class="btn" data-dismiss="modal">
                                    <i class="flaticon-cancel-12"></i> {{ __('messages.cancel') }}
                                </button>
                                <button type="submit" class="btn btn-primary">{{ __('messages.add') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div id="add_new_video_Modal" class="modal animated zoomInUp custo-zoomInUp" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('messages.add_new_media') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                        <form action="{{route('media.store.video')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="custom-file-container" data-upload-id="myFirstImage">
                                    <label>{{ __('messages.upload') }} ({{ __('messages.multiple_images') }}) <a
                                            href="javascript:void(0)" class="custom-file-container__image-clear"
                                            title="Clear Image">x</a></label>
                                    <label class="custom-file-container__custom-file">
                                        <input type="file" required name="video" multiple
                                               class="custom-file-container__custom-file__custom-file-input"
                                               accept="video/*">
                                        <input type="hidden" name="MAX_FILE_SIZE" value="10485760"/>
                                        <span class="custom-file-container__custom-file__custom-file-control"></span>
                                    </label>
                                    <div class="custom-file-container__image-preview"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn" data-dismiss="modal">
                                    <i class="flaticon-cancel-12"></i> {{ __('messages.cancel') }}
                                </button>
                                <button type="submit" class="btn btn-primary">{{ __('messages.add') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endsection
        @section('scripts')
            <!-- BEGIN PAGE LEVEL SCRIPTS -->
                <script src="/admin/plugins/lightbox/photoswipe.min.js"></script>
                <script src="/admin/plugins/lightbox/photoswipe-ui-default.min.js"></script>
                <script src="/admin/plugins/lightbox/custom-photswipe.js"></script>
                <!-- END PAGE LEVEL SCRIPTS -->
                <script>
                    var media_id;
                    $(document).on('click', '#btn_media', function () {
                        media_id = $(this).data('media-id');
                        $("#txt_media_id").val(media_id);
                    });
                </script>
@endsection

