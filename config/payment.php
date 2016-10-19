<?php
return [
  'pay_list'=>[
      "pay_packages"=>[
          ['code'=>"152.1", 'amount'=>11000, "product_name"=>"Nạp 11.000 đ vào tài khoản"],
          ['code'=>"152.2", 'amount'=>200000, "product_name"=>"Nạp 200.000 đ vào tài khoản"],
          ['code'=>"152.3", 'amount'=>300000, "product_name"=>"Nạp 300.000 đ vào tài khoản"],
          ['code'=>"152.4", 'amount'=>400000, "product_name"=>"Nạp 400.000 đ vào tài khoản"],
          ['code'=>"152.5", 'amount'=>500000, "product_name"=>"Nạp 500.000 đ vào tài khoản"],
          
      ],
      "pay_gate"=>[
          "112.1"=>"1pay-bank-charging"
      ]
  ],
  '1pay_code'=>[
      '00'=>'Giao dịch thành công.',
      '01'=>'Ngân hàng từ chối thanh toán: thẻ/tài khoản bị khóa.',
      '02'=>'Thông tin thẻ không hợp lệ.',
      '03'=>'Thẻ hết hạn.',
      '04'=>'Lỗi người mua hàng: Quá số lần cho phép. (Sai OTP, quá hạn mức trong ngày).',
      '05'=>'Không có trả lời của Ngân hàng.',
      '06'=>'Lỗi giao tiếp với Ngân hàng.',
      '07'=>'Tài khoản không đủ tiền.',
      '08'=>'Lỗi dữ liệu.',
      '09'=>'Kiểu giao dịch không được hỗ trợ.',
      '10'=>'Giao dịch không thành công.',
      '11'=>'Giao dịch chưa xác thực OTP.',
      '12'=>'Giao dịch không thành công, số tiền giao dịch vượt hạn mức ngày.',
      '13'=>'Thẻ chưa đăng ký Internet Banking',
      '14'=>'Khách hàng nhập sai OTP.',
      '15'=>'Khách hàng nhập sai thông tin xác thực.',
      '16'=>'Khách hàng nhập sai tên chủ thẻ.',
      '17'=>'Khách hàng nhập sai số thẻ.',
      '18'=>'Khách hàng nhập sai ngày phát hành thẻ.',
      '19'=>'Khách hàng nhập sai ngày hết hạn thẻ.',
      '20'=>'OTP hết thời gian hiệu lực.',
      '21'=>'Quá thời gian thực hiện request (7 phút) hoặc OTP timeout.',
      '22'=>'Khách hàng chưa xác thực thông tin thẻ.',
      '23'=>'Thẻ không đủ điều kiện thanh toán (Thẻ/Tài khoản không hợp lệ hoặc TK không đủ số dư).',
      '24'=>'Giao dịch vượt quá hạn mức một lần thanh toán của ngân hàng.',
      '25'=>'Giao dịch vượt quá hạn mức của ngân hàng.',
      '26'=>'Giao dịch chờ xác nhận từ Ngân hàng.',
      '27'=>'Khách hàng nhập sai thông tin bảo mật thẻ.',
      '28'=>'Giao dịch không thành công do quá thời gian quy định.',
      '29'=>'Lỗi xử lý giao dịch tại hệ thống Ngân hàng.',
      '99'=>'Không xác định.',
  ],
   'account'=>[
       ['code'=>"331.1", "name"=>"Trả tiền thuê sách"],
       ['code'=>"131.1", "name"=>"Đặt cọc thuê sách"],
   ]

];