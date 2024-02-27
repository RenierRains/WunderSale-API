<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Item;
use App\Models\Category;

use Illuminate\Support\Facades\Gate;


class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    public function index()
    {
        $categories = Category::all();
        $items = Item::inRandomOrder()->take(8)->get();
        
    
        return view('items.index', compact('categories', 'items'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'quantity' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048', //image file
            
        ]);

        $data = $request->only(['name', 'description', 'price', 'category_id','quantity']);
        $data['user_id'] = Auth::id(); // user to item association 

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        Item::create($data);

        return redirect()->route('items.index')->with('success', 'Item created successfully.');
    }

    public function show(Item $item)
    {
        $randomItems = Item::where('id', '!=', $item->id)->inRandomOrder()->take(4)->get(); 
        return view('items.show', compact('item', 'randomItems'));
    }

    public function edit(Item $item)
    {
        if (! Gate::allows('update-item', $item)) {
            abort(403);
        }

        $categories = Category::all(); 
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        if (! Gate::allows('update-item', $item)) {
            abort(403);
        }
    
        $request->validate([
            'name' => 'sometimes|max:255',
            'description' => 'required',
            'price' => 'sometimes|numeric',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'quantity' => 'required|integer|min:1',
        ]);

        $data = $request->only(['name', 'description', 'price', 'category_id','quantity']);

        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($item->image) {
                Storage::delete($item->image);
            }
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        $item->update($data);
        \Log::info('Update method called', $request->all());

        return redirect()->route('items.show', $item->id)->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        if ($item->image) {
            Storage::delete($item->image);
        }
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }

    public function userItems()
    {
        $items = Auth::user()->items;

        return view('items.user_items', compact('items'));
    }

    
}