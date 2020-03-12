<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ProductType;

class ParcelIntegrate extends Model
{
    public function getParcelItems($input_items) 
    {
        $vmb_items = collect();

        $items = json_decode($input_items);
        
        foreach($items as $item)
        {
            if(!$item->is_deleted)
            {
                $product_type = ProductType::find($item->product_type_id);
                
                if($product_type && $product_type->is_vmb_parcel)
                {
                    $item->type = "vmbparcel";
                    $vmb_items->push($item);
                }
                else if($product_type && $product_type->is_topup)
                {
                    $item->type = "topup";
                    $vmb_items->push($item);
                }
            }
        }
        return $vmb_items;
    } 
}
