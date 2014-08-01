=== Chained Quiz ===
Contributors: prasunsen
Tags: quiz, exam, test, questionnaire, survey
Requires at least: 3.3
Tested up to: 3.9.1
Stable tag: trunk
License: GPL2

Create a quiz where the next question depends on the answer to the previous question. 

== License ==

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

== Description ==

This is an unique quiz plugin that lets you create quizzes where the next question depends on the answer to the previous question. 

**To publish a quiz place its shortcode in a post or page**

###Features###

- Create unlimited number of quizzes and questions
- Questions support: single-choice, multiple-choice, open-end (essay)
- Assign points to each answer
- Calculate result based on the points (unlimited number of results and from/to points)
- Define what to do when specific answer is chosen - Go to next question in the row, go to a selected question, or finish the quiz

This unique quiz plugin lets you guide the user through the questions in the way you want. It's not only a very powerful tool for creating exams and quizzes, but can be used also to funel a sales process depending on user's selection.

== Installation ==

1. Unzip the contents and upload the entire `chained-quiz` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to "Chained Quiz" in your menu and manage the plugin
4. To publish a quiz place its shortcode in a post or page

== Frequently Asked Questions ==

None yet, please ask in the forum

== Screenshots ==

1. The create / edit quiz form lets you give a title and specify the dynamic end output

2. Here is how the different choices can be connected to different outcomes (plus assigning points at the same time)

3. And of course you can define different results depending on the total points collected in the quiz 

== Changelog ==

= Version 0.7.1 =
- Changed the way open-end questions work. If user's answer doesn't match any of your answers, they'll be sent to the next question instead of finalizing the quiz
- Fixed problem with showing open-end questions in the "view results" page

= Version 0.7 =
- Now the detailed answers and the path user walked will be stored, and can be seen in the "View submissions" page.
- Added sorting on the "View Submissions" page
- Added auto-scroll to the top of next question (useful if you have long questions)
- Added hyperlink to see the quiz when it is published in a post or page. If quiz has no hyperlink this means it's not yet published.
- Added classes around choices for better CSS control as suggested by iisisrael @ wordpress.org
- Answering question is now always required to avoid premature ending of the quiz
- Fixed problems with processing open-end questions
- Fixed bug with slashes shown when you have quotes in the result description (final screen)

= Version 0.6 = 
- Added option to automatically continue when radio button is checked
- Fixed bugs with multiple-select questions

= Version 0.5.7 =

First public release