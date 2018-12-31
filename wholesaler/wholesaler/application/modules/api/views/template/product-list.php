
<div>
    <h2>Product List</h2>
   
    <div> 
        
        <table> 
          
                <tr> 
                    
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category</th> 
                    <th>Description</th> 
                    <th>Quantity</th>
                   
                </tr> 
          


                <?php if (!empty($product_list)){   

                	
            	
                                                       
                foreach ($product_list as $value) { ?>
                    <tr>
                       
                        <td><?= $value->product_name; ?></td>
                        <td><?= $value->price; ?></td> 
                        <td><?= $value->category_name; ?></td>
                        <td><?= $value->description; ?></td>
                        <td><?= $value->quantity; ?></td>
                    </tr>
                <?php }
			}
         
         		?>
          
        </table>
    </div>
</div>