/*
	pages [
		page obj {
			number (display-text-wise)
			title (if necessary or applicable; false if not)
			content (as HTML)
			choices [
				{
					target page number (will be converted to 0-based index
					choice text (instruction for this choice)
				}
				+..
			]
		}
		+..
	]
*/
var pages = [

// PAGE START
	{
		number: "1",
		title: "Contents Page",
		content: "Wow, this is the start of an amazing adventure, huh? Looks like there's only one way to progress here. What will you do?",
		choices: [
			{
				target_page: 2,
				choice_text: "The first page of the real challenge starts NOW after some really looooong test text to test the layout"
			},
			{
				target_page: 2,
				choice_text: "You go away form the contents page."
			},
			{
				target_page: 2,
				choice_text: "You head to the North"
			}
		]
	},
// PAGE START
	{
		number: "2",
		title: "Page One and a flarf",
		content: "Wow, this is AN ENTIRELY NEW PAGE BECAUSE IMAGINATION",
		choices: [
			{
				target_page: 1,
				choice_text: "DEATH HATH FORSAKE"
			},
			{
				target_page: 1,
				choice_text: "You go back to the contents page"
			},
			{
				target_page: 1,
				choice_text: "You head to the south (Toward the village of Conned Ents)"
			}
		] // CHOICES END
	}, // PAGE END
];