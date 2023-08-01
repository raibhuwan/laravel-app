@extends('backend.users.layout')
@section('title', trans('strings.backend.images.edit.edit_image'))
@section('subheader')
    {{ trans('strings.backend.images.image') }}
@endsection

@section('users.layout')
    <div class="m-portlet__body">
        <div class="my-gallery" itemscope itemtype="http://schema.org/ImageGallery">
            <!--begin::Content-->
            <div class="tab-content">
                <div class="tab-pane active" id="m_widget5_tab1_content" aria-expanded="true">
                    @for ($i = 1; $i <= 6; $i++)

                        <div class="m-section">
                            <h4 class="m-section__heading">
                                {{ trans('strings.backend.images.image_number') . ' '.$i }}
                            </h4>
                        </div>
                        <div class="row">
                            <input type="hidden" name="image-number" value="{{$i}}">
                            <div class="col-lg-3">
                                <div class="m-demo" data-code-preview="true" data-code-html="true" data-code-js="false">

                                    <div class="m-demo__preview">
                                        <figure class="find-them-all" itemprop="associatedMedia" itemscope
                                                itemtype="http://schema.org/ImageObject">
                                            <a href="{{HelperFunctions::imageLink($image, $i)}}"
                                               class="m-widget7__img image-number_{{$i}}" itemprop="contentUrl"
                                               data-size="1024x1024">
                                                <img src="{{HelperFunctions::imageLink($image, $i)}}"
                                                     class="profile_image image-number_{{$i}}"
                                                     itemprop="thumbnail"
                                                     alt="Image description"
                                                     data-med-size="1024x1024"/>
                                            </a>

                                            <figcaption
                                                    itemprop="caption description"> {{ trans('strings.backend.images.image_number') . ' '.$i }}</figcaption>
                                        </figure>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5">

                                {{ Form::open(['url' => route('image.tempStore'), 'class' => 'm-dropzone dropzone m--margin-bottom-20', 'id' => "my-dropzone-container-". $i]) }}
                                <div class="m-dropzone__msg dz-message needsclick">
                                    <h3 class="m-dropzone__msg-title">{{ trans('strings.backend.images.drop_image_to_upload') }}</h3>
                                    <span class="m-dropzone__msg-desc">{{ trans('strings.backend.images.please_select_image') }}</span>
                                </div>
                                {{--</form>--}}
                                {{ Form::close() }}

                            </div>
                            <div class="col-lg-4 m--align-right">
                                @if($i == 1)
                                    <button type="button"
                                            class="btn btn-danger disabled"
                                            data-container="body"
                                            data-toggle="m-tooltip"
                                            data-placement="bottom" title=""
                                            data-original-title="You cannot delete image number 1">
                                        {{ trans('strings.backend.images.delete.delete_btn') }}
                                    </button>
                                @else

                                    <button type="button" name="image-delete-button"
                                            class="btn btn-danger btn-image-delete_{{$i}} {{(HelperFunctions::imageDelete($image, $i)) ? '' : 'disabled'}}"
                                            title="" data-toggle="modal"
                                            data-target="{{(HelperFunctions::imageDelete($image, $i)) ? '#modal1' : ''}}">
                                        {{ trans('strings.backend.images.delete.delete_btn') }}
                                    </button>

                                @endif
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed m-separator--lg"></div>
                    @endfor
                </div>
            </div>

            <!--end::Content-->
        </div>
    </div>

    @include('backend.users.images.delete')
    <!-- Root element of PhotoSwipe. Must have class pswp. -->
    <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

        <!-- Background of PhotoSwipe.
        It's a separate element, as animating opacity is faster than rgba(). -->
        <div class="pswp__bg"></div>

        <!-- Slides wrapper with overflow:hidden. -->
        <div class="pswp__scroll-wrap">

            <!-- Container that holds slides. PhotoSwipe keeps only 3 slides in DOM to save memory. -->
            <div class="pswp__container">
                <!-- don't modify these 3 pswp__item elements, data is added later on -->
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

                    {{--<button class="pswp__button pswp__button--share" title="Share"></button>--}}

                    <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>

                    <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>

                    <!-- Preloader demo https://codepen.io/dimsemenov/pen/yyBWoR -->
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

@endsection

@section('after-scripts')
    <script src="{{asset('js/photoswipe.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/cropper.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/photoswipe-ui-default.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/photoswipe-custom.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/cropper-custom.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/deleteImage.js')}}" type="text/javascript"></script>
@endsection
