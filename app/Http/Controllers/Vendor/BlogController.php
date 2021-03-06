<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Blog;
use App\Category;
use App\File;
use App\BlogCategory;
use Illuminate\Support\Str;
use App\Http\Requests\BlogRequest;

class BlogController extends Controller
{

    protected $blog;
    public function __construct(Blog $blog){
        $this->blog = $blog;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blogs = $this->blog->orderby('id','desc')->get();
        return view('vendor.blog.index',compact('blogs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $allCategory = Category::get();
        return view('vendor.blog.create',compact('allCategory'))->with('blog',$this->blog);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BlogRequest $request)
    {
        $request['user_id'] = auth()->id();
        if($request->title != ''){
            $request['slug'] = $this->createSlug($request->title);
        }
        if($request->hasFile('blog_image')){
            $data['user_id'] = $request['user_id'];
            $data['type'] = $request->blog_image->extension();
            $data['filepath'] = $request->blog_image->getClientOriginalName();
            $file = $request->blog_image->storeAS('images',$data['filepath'],'public');
            $upload = File::create($data);
            $request['file_id'] = $upload->id;
        }else{
            session()->flash('danger','Choose image blog');
            return redirect()->back()->withInput();
        }
   
       
        $blog = $this->blog->create($request->except(['_token','blog_image','category']));
        

        BlogCategory::create(['category_id'=>$request['category'],
                               'blog_id'=>$blog['id'] 
                            ]);

        session()->flash('success','Inserted Successfully');
        return redirect()->route('blog.edit',$blog->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $blog = $this->blog->with('file','blogcategory')->find($id);
        $allCategory = Category::get();
        return view('vendor.blog.create',compact('blog','allCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BlogRequest $request, $id)
    {
        if($request->title != ''){
            $request['slug'] = $this->createSlug($request->title);
        }
        if($request->hasFile('blog_image')){
            $data['user_id'] = $request['user_id'];
            $data['type'] = $request->blog_image->extension();
            $data['filepath'] = $request->blog_image->getClientOriginalName();
            $file = $request->blog_image->storeAS('images',$data['filepath'],'public');
            $upload = File::create($data);
            $request['file_id'] = $upload->id;
        }
        $blog = $this->blog->where('id',$id)->update($request->except(['_method','_token','blog_image','category']));
        if($request->category){
            $check = BlogCategory::where('blog_id' ,$id)->delete();
            BlogCategory::create(['category_id'=>$request['category'],
                                'blog_id'=>$id 
                                ]); 
        }
        session()->flash('success','Updated Successfully');
        return redirect()->route('blog.edit',$id);  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function createSlug($title, $id = 0)
    {
        // Normalize the title
        $slug = Str::slug($title);

        // Get any that could possibly be related.
        // This cuts the queries down by doing it once.
        $allSlugs = $this->getRelatedSlugs($slug, $id);

        // If we haven't used it before then we are all good.
        if (! $allSlugs->contains('slug', $slug)){
            return $slug;
        }

        // Just append numbers like a savage until we find not used.
        for ($i = 1; $i <= 10; $i++) {
            $newSlug = $slug.'-'.$i;
            if (! $allSlugs->contains('slug', $newSlug)) {
                return $newSlug;
            }
        }

        throw new \Exception('Can not create a unique slug');
    }

    protected function getRelatedSlugs($slug, $id = 0)
    {
        return Blog::select('slug')->where('slug', 'like', $slug.'%')
            ->where('id', '<>', $id)
            ->get();
    }


}
