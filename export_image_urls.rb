# سكريبت Ruby لتصدير جميع روابط الصور من Rails Active Storage
# نفذ هذا السكريبت من Rails console أو كملف منفصل

# طريقة الاستخدام من Rails Console:
# rails console
# ثم انسخ والصق الكود التالي:

urls = []
host = 'https://white-style.ps'

puts "جاري جلب روابط صور المنتجات..."
Product.includes(image_attachment: :blob).find_each do |product|
  if product.image.attached?
    url = Rails.application.routes.url_helpers.rails_blob_url(product.image, host: host)
    urls << "#{url} # Product: #{product.title} (ID: #{product.id})"
  end
end
puts "  ✓ تم جلب #{urls.count} صورة منتج"

initial_count = urls.count

puts "جاري جلب روابط صور السلايدر..."
Slider.includes(image_attachment: :blob).find_each do |slider|
  if slider.image.attached?
    url = Rails.application.routes.url_helpers.rails_blob_url(slider.image, host: host)
    urls << "#{url} # Slider: #{slider.title} (ID: #{slider.id})"
  end
end
puts "  ✓ تم جلب #{urls.count - initial_count} صورة سلايدر"

initial_count = urls.count

puts "جاري جلب روابط صور الأقسام..."
Category.includes(image_attachment: :blob).find_each do |category|
  if category.image.attached?
    url = Rails.application.routes.url_helpers.rails_blob_url(category.image, host: host)
    urls << "#{url} # Category: #{category.title} (ID: #{category.id})"
  end
end
puts "  ✓ تم جلب #{urls.count - initial_count} صورة قسم"

initial_count = urls.count

puts "جاري جلب روابط صور البراندات..."
Brand.includes(image_attachment: :blob).find_each do |brand|
  if brand.image.attached?
    url = Rails.application.routes.url_helpers.rails_blob_url(brand.image, host: host)
    urls << "#{url} # Brand: #{brand.name} (ID: #{brand.id})"
  end
end
puts "  ✓ تم جلب #{urls.count - initial_count} صورة براند"

puts ""
puts "=" * 50
puts "إجمالي الصور: #{urls.count}"
puts "=" * 50

# حفظ في ملف
output_file = Rails.root.join('image_urls.txt')
File.write(output_file, urls.join("\n"))

puts ""
puts "✓ تم حفظ الروابط في: #{output_file}"
puts ""
puts "الخطوة التالية:"
puts "1. حمّل ملف image_urls.txt من السيرفر"
puts "2. ضعه في: D:\\localProjects\\whiteStyle\\"
puts "3. نفذ: .\\download_from_urls.ps1"
