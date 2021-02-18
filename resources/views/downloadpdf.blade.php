
<link rel="stylesheet" media="all" href="{{ asset('css/bootstrap.css')}}">
<link rel="stylesheet" href="{{ asset('css/_variables.scss')}}">
<link rel="stylesheet" href="{{ asset('css/custom.sass')}}">

<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>-->
<style>
    .gap{
        margin-top: 30px;
    }
    .wbprimary{
        color:#DF691A;
    }
    .wbgreen{
        color:#5cb85c;
    }
    .table-responsive { overflow-x: visible !important; }
    .wbtd th{
        background-color: #DF691A;
        color:aliceblue;
    }
    .wbfont1{
        font-size: 25px;
    }
    .wbbackground{
        background-color: #DF691A;
        color: aliceblue;
    }
    .container {
  padding-right: 15px;
  padding-left: 15px;
  margin-right: auto;
  margin-left: auto;
}
.row {
  margin-right: -15px;
  margin-left: -15px;
}
.column {
  float: left;
  width: 50%;
  padding: 10px;
  height: 300px; 
}
</style>
<body >
<div id="header" class="container">        
       
    <div class="row" style="margin-top:30px;margin-bottom: 50px;"> 
        <div class="col-md-4 gap" >
            <img style="height:50px;width:200px" src="{{ asset('img/wblogo.jpg')}}" >     
        </div>  
        <div class="col-md-8 text-right">
            <p class="text-right" > 
                <h1>Work Order No 150</h1>
                <h5>Issued Date - 22-10-2020</h5>
                <h5>Work Location - Perumbavoor</h5>
                <h5>Scope - Labor Only</h5>
            </p>
            
        </div>    
        
        
    </div>
    <div class="row" >
        <div class="col-md-12">
            <span>This agreement is made on <b>22-10-2020</b> between</span>
        
        </div>
    </div>
    <div class="row gap" >
        <div class="col-md-5">
            <address>
                <strong>Example Inc.</strong><br>
                1234 Example Street<br>
                Antartica, Example 0987<br>
                <span class="glyphicon glyphicon-earphone"></span> (123) 456-7890
              </address>
              

        </div>
        <div class="col-md-2 text-center">&</div>
        
        <div class="col-md-5 text-right">
            <address>
                <strong>M/s. Vevees Constructions</strong><br>
                1V/70 N,2 nd Floor ,K.M.V Arcade,<br>
                Puthuppanam,Kolenchery<br>
                <span class="glyphicon glyphicon-earphone"></span> 7034021109
              </address>
             
        </div>
        

    </div>
    <div class="row gap" >
        <div class="col-md-6 text-left">
            <p>Hereinafter called the OWNER, which term
                shall include his assigns and successors
             </p>
        </div>
        <div class="col-md-6 text-right">
            <p>Hereinafter refered to as CONTRACTOR
                which term shall include his assigns and
                successors for the Performance of Services
                as refered to in the SCOPE OF WORK in
                Accordance with the Terms & Conditions
                Stipulated herein.
                </p>
        </div>
    </div>  

</div>
<div  id="spec-container" class="container gap"> 
    <div class="card">
        <div class="card-header wbbackground">
            Work Order Specifications
        </div>
        <div class="card-body"> 
   
    
        <table class="table">
            <thead>
            <tr style="border-top: hidden">
                <th style="width:70%" >Details</th>
                <th style="width:30%" class="text-center">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <p><h6 >Constructing the Foundation and Super structure of the Proposed
                         Residence as per the approved Plan</h6></p>
                    <p> This is a sample comment and is expanded to see how it looks like when
                        there is huge amount of text to be displayed.The content is still not over,
                    there is much more to be added so that it falls to the next line</p>
                    <p >Qty- 2736  sqft <small class="wbprimary">(Rs.1100 per square feet)</small></p>
                </td> 
                <td class="text-center"><p><h6 >Rs.3000000</h6></p></td>
            </tr>
            <tr>
                <td>
                    <p><h6 >Earth work in ordinary soil</h6></p>
                    <p> Constructing the D R M at 60 cm width and 60 cm Height
                    </p>
                    <p >Qty- 30  sqft <small class="wbprimary">(Rs.220 per square feet)</small></p>
                </td> 
                <td class="text-center"><p><h6 >Rs.100000</h6></p></td>
            </tr>
            <tr>
                <td>
                    <p><h6 >This is a dummy line item</h6></p>
                    <p> This is an ordinary dummy comment and its not very large
                    </p>
                    
                </td> 
                <td  class="text-center"></td>
            </tr>
            <tr>
                <td>
                    <p><h6 >This is a dummy line item</h6></p>
                    <p> This is an ordinary dummy comment and its not very large
                    </p>
                    
                </td> 
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>
                    <p><h6 >This is a dummy line item</h6></p>
                    <p> This is an ordinary dummy comment and its not very large
                    </p>
                    
                </td> 
                <td class="text-center"></td>
            </tr>
            <tr>
                <td>
                    <p><h6 >This is a dummy line item</h6></p>
                    <p> This is an ordinary dummy comment and its not very large
                    </p>
                    
                </td> 
                <td class="text-center"></td>
            </tr>
        </tbody>
        </table>
    
    <div class="row wbfont1">
        <div class="col-md-3"><p>Subtotal</p></div>
        <div class="col-md-9 text-right"><p>Rs.4000000</p></div>
    </div>
    <div class="row wbfont1">
        <div class="col-md-3"><p>In words</p></div>
        <div class="col-md-9 text-right"><p>Rupees Fourty Lakh Only</p></div>
    </div>    
    </div>
    
    </div>
</div>  

<div  id="deliverables-container" class="container gap" > 
    <div class="card">
        <div class="card-header wbbackground">
            Key Deliverables
        </div>
        <div class="card-body">          
          <p class="card-text">
              <span class="glyphicon glyphicon-check wbprimary"></span>
              <span>The mortar gaps of block works is not more than specified thickness
            </span>
        </p>
            <p class="card-text">
                <span class="glyphicon glyphicon-check wbprimary"></span>
                <span>Vertical alignment of brick wall must be plumb
              </span>
            </p>
              <p class="card-text">
                <span class="glyphicon glyphicon-check wbprimary"></span>
                <span>The corner portions of brick wall must be right angle
              </span>
            </p>
              <p class="card-text">
                <span class="glyphicon glyphicon-check wbprimary"></span>
                <span>The mortar gaps of block works is not more than specified thickness
              </span>
            </p>
              <p class="card-text">
                <span class="glyphicon glyphicon-check wbprimary"></span>
                <span>Ensure the block work flushed & levelled with plinth beam in toilet portion

              </span>
            </p>
          </p>
          
        </div>
      </div>
      

</div>
<div  id="tc-container" class="container gap" > 
    <div class="card">
        <div class="card-header wbbackground">
            Terms & Conditions
        </div>
        <div class="card-body"> 
        <h5 class="card-title">1. Exclusive Agreement</h5>         
          <p class="card-text">
            This Work Order (W.O) constitutes the exclusive agreement between the parties hereto. Any Special terms and conditions mentioned in the W.O shall take precedence over these Standard
            Terms and 'Conditions'.These â€˜Standard Terms and Conditionsâ€™ cannot be changed without prior written consent of both parties. Additionally, the goods and services shall conform to
            specifications,drawings and any other description attached hereto and shall be free from defects in materials and workmanship.
            
         </p>  
         <h5 class="card-title">2. Work Timings </h5>         
          <p class="card-text">
            The normal site working timing shall be 8 am to 6 Pm , however the contractor have privilege to extend the work time by provide special request to the customer</p>
         
            <table class="table gap">
                <tr style="border-top: hidden">
                    <th style="width: 55%">Work Stages</th>
                    <th class="text-center" style="width: 15%">Start Date</th>
                    <th class="text-center" style="width: 15%" >Duration</th>
                    <th class="text-center" style="width: 15%">End Date</th>
                </tr>
                <tr>
                    <td>Foundation Work</td>
                    <td class="text-center" >12/09/2020</td>
                    <td class="text-center">  21 </td>
                    <td class="text-center" >05/10/2020</td>
                </tr>
                <tr>
                    <td>G.F Block work above Super structure</td>
                    <td class="text-center" >01/11/2020</td>
                    <td class="text-center" >10 </td>
                    <td class="text-center" >12/11/2020</td>
                </tr>
                <tr>
                    <td >G.F Lintel & sushade </td>
                    <td class="text-center" >13/11/2020</td>
                    <td class="text-center" >8 </td>
                    <td class="text-center" >21/11/2020</td>
                </tr>
                <tr>
                    <td>Foundation Work</td>
                    <td class="text-center" >12/09/2020</td>
                    <td class="text-center" >21 </td>
                    <td class="text-center" >05/10/2020</td>
                </tr>
                <tr>
                    <td>G.F Block work above Super structure</td>
                    <td class="text-center" >01/11/2020</td>
                    <td class="text-center" >10 </td>
                    <td class="text-center" >12/11/2020</td>
                </tr>
                <tr>
                    <td>G.F Lintel & sushade </td>
                    <td class="text-center" >13/11/2020</td>
                    <td class="text-center" >8 </td>
                    <td class="text-center" >21/11/2020</td>
                </tr>
                </table>
            
           
         
         <h5 class="card-title">3. Payment </h5> 
         <p>Customer has to transfer the agreed contract value as per the payment schedule mentioned in the work order only to the escrow account of M/s Infrasynergics Private limited with Account
        details as mentioned below for making payments to contractors/associates & material vendors Account Name: Infrasynergics Private Limited Account
        Number:4805002100001029 Bank: Punjab National bank Branch: Kolenchery IFSC:PUNB0480500 All payments will be made directly to the contractor/associate after
        verifying work quality & stage based completion. The customer is not liable to pay any fee, commissions or any other payments other than the agreed contract value for the works
        mentioned in the work order.</p>
         <table class="table gap"> 
             <tr style="border-top: hidden">
                 <th  style="width: 50%">Payment Stages</th>
                 <th class="text-center" style="width: 25%">Amount in Rupees</th>
                 <th class="text-center" style="width: 25%"> Payment Date</th>
             </tr>
             <tr>
                 <td >Advance</td>
                 <td class="text-center">50200</td>
                 <td class="text-center">20/11/2020</td>
             </tr>
             <tr>
                <td >Completion of foundation</td>
                <td class="text-center">30096</td>
                <td class="text-center">20/11/2020</td>
            </tr>
            <tr>
                <td >Starting of Ground floor Lintel work</td>
                <td class="text-center">52114</td>
                <td class="text-center">20/11/2020</td>
            </tr>
            <tr>
                <td >Starting of Out side Plastering</td>
                <td class="text-center">30000</td>
                <td class="text-center">20/11/2020</td>
            </tr>
         </table>

         <h5 class="card-title">4. Rejection/Termination </h5> 
         <p class="card-text">
            1.The customer can terminate the work order on the following:<br>
                a. Misbehavior during work time.<br>
            b. Mishandling the materials.<br>
            2. The Associate can terminate the work order on the following:<br>
            a. Misbehavior during work time.<br>
            b.  Not receiving the payment as agreed.
            </p>

            <h5>5. Cancellation</h5>
            <p>Inframall reserves the right to cancel this W.O, by giving notification to the Associate.</p>
            <h5>6. Safety Measures</h5>
            <p>During the site work the sub-Contract(Associate) need to follow the basic safety rules in the construction industries. If the sub-Contract(Associate) employees who are working on
            roof,balcony they should wear safety belt. Any Issue/Accidents in the site will be under the risk of 2nd Party(Contractor)</p>
            <h5>7. Storage</h5>
            <p>The customer has to provide sufficient secured storage space to the contractor free of cost.</p>
            <p>8. Changes in the scope, specification, work schedule etc should be brought to the notice of Inframall by both Customer & Contractor & should be incorporated in the agreement as
            amendment.</p>
            <h5>9. First Aid Kit</h5>
            <p>First Aid Kit will be provided by 1st Party(Customer).</p>
            <h5>10. Legal</h5>
            <p>If at any time a dispute, difference, or disagreement shall arise between the parties either parties can approach a court of law. In Lieu of breaching this work order neither party shall take
            any legal action against the third party.</p>
            <p>11. Electricity and water to be provided by the customer free of cost at the site for the construction and related services.</p>
            <p>12. All day to day updations regarding co ordination has to be done well in advance by both parties.</p>
            <p>13. Associate should properly clean the work space and related premises after completion.</p>
            <p>14. Associate should ensure his work does not effect any other works or other materials in the premises that belong/does not belong to the scope of
            this particular work</p>
            <p>15. Any specification changes or additional work from the current work order will attract additional charges
            <p>16. Progress of work will be on the basis of clearance of stage wise payment
            Customer's Sign Contractor's Sign</p>
            <p>17. Union issues related to material unloading/loading should be sorted out by the customer</p>
            <p>18. Sufficient storage space should be provided by the customer for materials related to current work</p>
            <p>19. After finishing the work , the final amount will be settled as per the remeasured quantity</p>
            <p>20. All approvals and permits from local & statutory bodies needs to be obtained by the customer before starting the work.</p>
            <p>21. All approvals and permits from local & statutory bodies needs to be obtained by the Associate before starting the work.
            The Agreement has duly signed by the representatives of the Parties. The work order is read and understood by both parties and therefore
            signed for execution by authorized personal for each party.</p>

            <div class="container">
                <div class="row">
                    <div class="column">
                        <address>Mr.Jaison George<br>
                            Edhathala (H) ,Varikkoli P.O,<br>
                            PUTHENCRUZ<br>
                            Phone: 9605051020
                            </address>
                    </div>
                    <div class="column">
                        <address> M/s. Vevees Constructions<br>
                        1V/70 N,2 nd Floor ,K.M.V Arcade,<br>
                        Puthuppanam,Kolenchery,<br>
                        Phone: 7034021109</address>
                    </div>
                </div>
                <div class="row"><br><br><br></div>
                <div class="row">
                    <div class="column">Customer Signature</div>
                    <div class="column">Contractor Signature</div>
                </div>
            </div>
          
        </div>
      </div>
      

</div>
</body>