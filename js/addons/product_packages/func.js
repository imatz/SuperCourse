
function fn_pp_recalculate(get){
    var table_rows = $("#pp_picker table tr.cm-js-item");
    var price =0;
    var m_type ="";
    var mod=0;
    var amount=1;
    var mod_el;
    var amount_el;
    var total_price=0;
    var discounted_price =0;
    if(get){
        var count =0;
        var products=[];
        var product={};
    }
    
    
    table_rows.each(function(index,row){
        if(!$(row).hasClass("cm-clone")){
            if(get){
                product = {};              
            }
            price = parseFloat($(row).find("input.pp_price").val());
            
            m_type = $(row).find("select.pp_modifier_type").find(":selected").val();
            
            mod_el = amount_el = null;
            amount = 1;
            $(row).find("span.pp_discounted_price").each(function(i,e){
                if(/item_discounted_price_pp/.test($(e).prop("id"))){
                    mod_el = e;        
                    return;
                }
            })
            
            
            $(row).find("input").each(function(i,e){
                if(/^package_info.*\[amount\]$/.test($(e).prop("name"))){
                    amount_el = e;        
                    return;
                }
            })
            if(amount_el){
                amount = $(amount_el).val();
            }
            
           
            mod = parseFloat($(row).find("input.pp_modifier").val());
            if(!isNaN(price)){
                total_price+=price.toFixed(2)*amount;
            }
            if(get){
                product = {price:price,modifier_type:m_type,modifier:mod, amount:amount,modifier_element:$(row).find("input.pp_modifier"),modifier_type_element:$(row).find("select.pp_modifier_type"),amount_element:amount_el};
            }
            if(!isNaN(price) && m_type ){
                
                switch(m_type){
                    case "by_percentage":
                        price -=price*mod/100;
                        break;
                    case "by_fixed":
                        price -=mod;
                        break;
                    case "to_percentage":
                        price =price*mod/100;
                        break;
                    case "to_fixed":
                        price =mod;
                        break;
                    default:break;
                
                }    
            
                $(mod_el).text($.formatPrice(price));
                $(row).find("input.pp_f_price").val($.formatPrice(price));
                if(get){
                    count ++;
                }
            }
            
            if(!isNaN(price)){
                discounted_price+=price.toFixed(2)*amount;
            }
            if(get){
                product.discounted_price = price;
                products.push(product);
            }
        }
        
    });
    
    if(!isNaN(total_price)){
    //    console.log(total_price);
        $("span#sec_total_pp_price").text($.formatPrice(total_price));
        
    }
    
    if(!isNaN(discounted_price)){
        
        $("span#sec_discounted_pp_price").text($.formatPrice(discounted_price));
    }
    
    if(!isNaN(total_price) && (!isNaN(discounted_price))){
        $("span#sec_discount_pp").text($.formatPrice(total_price-discounted_price));
    }
    
    if(get){
        return {total_price:total_price,discounted_price:discounted_price,count_products:count,products:products};
    }
}

function fn_pp_apply_discount(event){
    var code;
    if(event){
        if(event.keyCode){
            code = event.keyCode;
        }
    }else{
        code = 13;
    }
    if(code==13){
        var discount_price = parseFloat($("#elm_pp_global_discount").val());
        var prices,discount;
        if(discount_price && (prices= fn_pp_recalculate(true))){
            discount = 1 - (prices.total_price-discount_price)/prices.total_price+0.0000001;
            if(discount>1){
                discount = 1; 
                discount_price = prices.total_price;
            }
            var modifier_type = "by_fixed";
            var discounted = 0;
            prices.products.forEach(function(product,i){
                product.modifier_type_element.find(":selected").removeAttr("selected");
                product.modifier_type_element.find("option").each(function(i,e){                    
                    if(e.value.localeCompare(modifier_type)==0){
                        $(e).attr("selected","selected");
                        return;
                    }
                });
                discounted = discount*product.price;                
                product.modifier_element.val(discounted.toFixed(2));
            });
            prices = fn_pp_recalculate(true);
            var delta;
            if((delta = prices.total_price-prices.discounted_price-discount_price)){
              //  console.log(delta);
                discount_price+=delta;
                $("#elm_pp_global_discount").val($.formatPrice(discount_price));
                /*var product = prices.products.shift();
                delta = product.modifier+delta/product.amount;
                product.modifier_element.val(delta.toFixed());
                fn_pp_recalculate();*/
            }
        }
    }
    
}
(function(_, $) {
    
    // Hook add_js_item
    $.ceEvent('on', 'ce.picker_add_js_item', function(data) {

        if (data['var_prefix'] == 'p') {
            price = parseFloat(data.item_id.price);
	  
            if (isNaN(price)) {
	    
                price = 0;
            }
            
            data['append_obj_content'] = data['append_obj_content'].str_replace('{pp_id}', data['item_id']['product_id']).str_replace('{price}', price);
            
            // Price replacement
            var content = $('<tr>' + data['append_obj_content'] + '</tr>');

            content.find('span[id*=\'price_pp\']').each(function() {
                $(this).text(price.toFixed(2));
            });

            data['append_obj_content'] = content.html();
        }
       
    });


    $.ceEvent('on', 'ce.picker_transfer_js_items', function(data) {
        
        for (var id in data) {
            
            data[id].price = parseFloat($('#price_' + id).val());
     
            if (data[id].option && data[id].option.path) {
                // We have options, try to find their price modifiers
                var modifier_set;
                for (var option_id in data[id].option.path) {
                    variant_id = data[id].option.path[option_id];
                    
                    modifier = parseFloat($('#pp_option_modifier_' + option_id + '_' + variant_id).val());
                    if (!isNaN(modifier)) {
                        data[id].price += modifier;
                       // console.log(modifier);
                       modifier_set = true;
                    }
                    
                }
                
                if(!modifier_set){
             
                    var default_options = $(".pp_def_option_modifier_"+id);
                    default_options.each(function(ind,el){
                        modifier = parseFloat($(el).val());
                        
                        if (!isNaN(modifier)) {
                            data[id].price += modifier;
                            modifier_set = true;
                        }
                    });
                }
            }
            data[id].test = true;
        }
        
    });
    

}(Tygh, Tygh.$));
