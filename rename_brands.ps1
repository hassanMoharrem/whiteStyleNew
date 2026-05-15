# سكريبت إعادة تسمية صور البراندات

$brandsFolder = "D:\localProjects\whiteStyle\white_style_images\brands"

# قائمة البراندات المتاحة
$availableBrands = @(
    "Adidas",
    "Nike",
    "THE NORTH FACE",
    "UNDER ARMOUR",
    "REEBOK",
    "TIMBERLAND",
    "BOSS",
    "EMPORIO ARMANI",
    "GUCCI",
    "LACOSTE",
    "PRADA",
    "POLO",
    "PULL SHARK",
    "ROBERTO VINO",
    "ZARA",
    "DIESEL",
    "LOUIS VUITTON",
    "W Jeans"
)

Write-Host "════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host " 🏷️  إعادة تسمية صور البراندات" -ForegroundColor Yellow
Write-Host "════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""

# الحصول على جميع الملفات
$files = Get-ChildItem $brandsFolder | Sort-Object Name

$renamedCount = 0

foreach ($file in $files) {
    Write-Host ""
    Write-Host "────────────────────────────────────────────" -ForegroundColor Gray
    Write-Host "الملف الحالي: " -NoNewline -ForegroundColor Yellow
    Write-Host "$($file.Name)" -ForegroundColor White
    Write-Host ""

    # محاولة التعرف التلقائي
    $suggestedName = $null

    if ($file.Name -match 'paul.*shark') {
        $suggestedName = "PULL SHARK"
    }
    elseif ($file.Name -match 'diesel') {
        $suggestedName = "DIESEL"
    }
    elseif ($file.Name -match 'nike') {
        $suggestedName = "Nike"
    }
    elseif ($file.Name -match 'adidas') {
        $suggestedName = "Adidas"
    }
    elseif ($file.Name -match 'north.*face') {
        $suggestedName = "THE NORTH FACE"
    }
    elseif ($file.Name -match 'armani') {
        $suggestedName = "EMPORIO ARMANI"
    }
    elseif ($file.Name -match 'gucci') {
        $suggestedName = "GUCCI"
    }
    elseif ($file.Name -match 'lacoste') {
        $suggestedName = "LACOSTE"
    }
    elseif ($file.Name -match 'prada') {
        $suggestedName = "PRADA"
    }
    elseif ($file.Name -match 'polo') {
        $suggestedName = "POLO"
    }
    elseif ($file.Name -match 'reebok') {
        $suggestedName = "REEBOK"
    }
    elseif ($file.Name -match 'timberland') {
        $suggestedName = "TIMBERLAND"
    }
    elseif ($file.Name -match 'boss') {
        $suggestedName = "BOSS"
    }
    elseif ($file.Name -match 'zara') {
        $suggestedName = "ZARA"
    }
    elseif ($file.Name -match 'louis.*vuitton|lv') {
        $suggestedName = "LOUIS VUITTON"
    }
    elseif ($file.Name -match 'roberto.*vino') {
        $suggestedName = "ROBERTO VINO"
    }

    if ($suggestedName) {
        Write-Host "اقتراح تلقائي: " -NoNewline -ForegroundColor Green
        Write-Host "$suggestedName" -ForegroundColor Cyan
        Write-Host ""
    }

    # عرض القائمة
    Write-Host "البراندات المتاحة:" -ForegroundColor Yellow
    for ($i = 0; $i -lt $availableBrands.Count; $i++) {
        $num = $i + 1
        Write-Host "  [$num] $($availableBrands[$i])" -ForegroundColor Gray
    }
    Write-Host ""
    Write-Host "  [S] تخطي هذا الملف (Skip)" -ForegroundColor DarkGray
    Write-Host "  [Q] إنهاء البرنامج (Quit)" -ForegroundColor DarkGray
    Write-Host ""

    if ($suggestedName) {
        Write-Host "اكتب رقم البراند، أو اضغط Enter للموافقة على الاقتراح: " -NoNewline -ForegroundColor Cyan
    } else {
        Write-Host "اكتب رقم البراند أو اسمه: " -NoNewline -ForegroundColor Cyan
    }

    $input = Read-Host

    # معالجة الإدخال
    if ($input -eq 'Q' -or $input -eq 'q') {
        Write-Host ""
        Write-Host "تم الإنهاء." -ForegroundColor Yellow
        break
    }

    if ($input -eq 'S' -or $input -eq 's' -or $input -eq '') {
        if ($input -eq '' -and $suggestedName) {
            $brandName = $suggestedName
        } else {
            Write-Host "تم التخطي." -ForegroundColor DarkGray
            continue
        }
    }
    elseif ($input -match '^\d+$') {
        $index = [int]$input - 1
        if ($index -ge 0 -and $index -lt $availableBrands.Count) {
            $brandName = $availableBrands[$index]
        } else {
            Write-Host "رقم غير صحيح!" -ForegroundColor Red
            continue
        }
    }
    else {
        $brandName = $input
    }

    # إعادة التسمية
    $extension = $file.Extension
    $newName = "$brandName$extension"
    $newPath = Join-Path $brandsFolder $newName

    # إذا كان الاسم موجود، أضف رقم
    if (Test-Path $newPath) {
        $counter = 1
        while (Test-Path $newPath) {
            $newName = "${brandName}_${counter}${extension}"
            $newPath = Join-Path $brandsFolder $newName
            $counter++
        }
    }

    try {
        Rename-Item -Path $file.FullName -NewName $newName -ErrorAction Stop
        Write-Host ""
        Write-Host "✓ تمت إعادة التسمية إلى: " -NoNewline -ForegroundColor Green
        Write-Host "$newName" -ForegroundColor White
        $renamedCount++
    }
    catch {
        Write-Host ""
        Write-Host "✗ فشل: $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host " ✓ تمت إعادة تسمية $renamedCount ملف" -ForegroundColor Green
Write-Host "════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""
Write-Host "اضغط أي زر للإغلاق..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
