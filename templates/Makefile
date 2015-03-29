make:
	for i in *.tpl;do sed -f script.sed "$$i" > "$${i%tpl}html"; done

clean:
	rm *.html
