@extends('vendor.layout', ['title'=> $blog->exists ? 'blog edit' : 'blog create'])

@section('header')
    <style>
        .profile-pic {
            /* max-width: 200px; */
            max-height: 200px;
            display: block;
        }
        .file-upload {
            display: none;
        }
        .circle {
            /* border-radius: 1000px !important; */
            overflow: hidden;
            width: 100%;
            height: auto;
            /* border: 8px solid rgba(100, 43, 43, 0.7); */
            /* position: absolute;   */
            top: 72px;
        }
        img.profile-pic{
            border-radius: 20px !important;
            max-width: 100%;
            width: 100%;
            height: auto;
        }
        .p-image {
        /* position: absolute;
        top: 167px;
        right: 30px; */
        color: #666666;
        transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
        }
        .p-image:hover {
        transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
        }
        .upload-button {
        font-size: 1.2em;
        }
        .upload-button:hover {
        transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
        color: #999;
        }
    </style>
@endsection
@section('content')
{!!Form::model($blog,[
    'route'=> $blog->exists ? ['blog.update',$blog->id] : ['blog.store'],
    'method'=>$blog->exists ? 'PUT' : 'POST',
    'id' => 'form_blog_id',
    'files' => true
])!!}
{{-- <form action="{{route('blog.store')}}" method="POST"> --}}
    @csrf
    <div class="form-group">
        <div class="row">
            <div class="col-md-12">
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
              <div class="circle upload-button mb-3">
                <!-- User Profile Image -->
                
                    @if(isset($blog->file) && $blog->file->filepath != '')
                        <img class="profile-pic" src="{{'/storage/images/'.$blog->file->filepath}}">
                    @else
                        <img class="profile-pic" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT64gAy5EStuM7QbJJAAcigv4rIAsUZk6xJQIhBrufeUlaaTE1mhRRZOBwiOAn83ifBQlY&usqp=CAU">
                    @endif 
                
                <!-- Default Image -->
                {{-- <i class="fa fa-user fa-5x"></i>  --}}
              </div>
              <div class="p-image">
                {{-- <i class="fa fa-camera upload-button"></i> --}}
                 <input class="file-upload" name="blog_image" type="file" accept="image/*"/>
              </div>
           </div>
        </div>
    </div>
    <div class="form-group mb-3">
        <label for="">Title</label>
        <input type="text" placeholder="Blog Title" name="title" class="form-control" value="{{isset($blog->title) && $blog->title  ? $blog->title : old('title')}}">
    </div>
    <div class="form-group mb-3">
        <label for="">Content</label>
        <textarea type="text" placeholder="Blog Title" rows="5" name="content" class="form-control">{{isset($blog->content) && $blog->content  ? $blog->content : old('content')}}</textarea>
    </div>
    <div class="form-group mb-3">
        <label for="">Category</label>
        <select name="category" id="" class="form-control">
            <option value="">Select Category</option>
            @forelse ($allCategory as $category)
                <option value="{{$category->id ?? ''}}" {{isset($blog->blogcategory->category_id) && $blog->blogcategory->category_id == $category->id ? 'selected': '' }}>{{$category->name ?? ''}}</option>
            @empty
                <option value="">No data found</option>
            @endforelse
        </select>
    </div>
    <div class="form-group">
        <input type="submit"  style="width: 100%;" class="btn btn-primary">
    </div>

   
{{-- </form> --}}
{{Form::close()}}
@endsection
@section('footer')
<script>
    $(document).ready(function() {
        var readURL = function(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                $('.profile-pic').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $(".file-upload").on('change', function(){
            readURL(this);
        });

        $(".upload-button").on('click', function() {
        $(".file-upload").click();
        });
    });
</script>
@endsection