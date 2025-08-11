<?php

$params = ['name', 'slab_width', 'slab_height', 'pad_width', 'pad_height', 'edges', 'site_url'];

foreach( $params as $param ) {

	if ( !isset($_GET[$param]) || empty($_GET[$param]) ) {

		echo '<h2 style="color:red">' . ucfirst($param) . ' is Missing</h2>';

		exit;

	}

}





?>

<!DOCTYPE html>

<html lang="en">

	<head>

		<meta charset="UTF-8">

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

		<title>"<?=$_GET['name']?>" Slab & Cutting Area MM Calculator</title>

		<style>

			body {

				margin: 0;

				padding: 0;

				font-family: Arial, sans-serif;

				overflow: hidden;

			}

			

			.heading {

				background: white;

				border: 1px solid #e0e0e0;

				padding: 25px;

				border-radius: 10px;

				box-shadow: 0 2px 10px rgba(0,0,0,0.08);

				margin-bottom: 30px;

			}



			.header-row {

				display: flex;

				flex-direction: column;

			}



			.title-section {

				width: 100%;

			}



			.calculator-title {

				font-weight: 700;

				font-size: 28px;

				color: #000;

				margin-bottom: 20px;

				display: block;

			}

			

			.stats-table {

				display: flex;

				gap: 30px;

				align-items: flex-start;

				border: 1px solid #ddd;

				border-radius: 8px;

				background: white;

				padding: 25px;

				box-shadow: 0 2px 8px rgba(0,0,0,0.1);

				justify-content: center;

			}

			

			.stat-column {

				display: flex;

				flex-direction: column;

				min-width: 220px;

				text-align: left;

				border: 1px solid #e0e0e0;

				border-radius: 8px;

				background: #f8f9fa;

				padding: 15px;

				box-shadow: 0 1px 4px rgba(0,0,0,0.05);

				transition: all 0.3s ease;

				position: relative;

			}
			
			.stat-column:hover {
				transform: translateY(-2px);
				box-shadow: 0 4px 12px rgba(0,0,0,0.15);
			}

			

			.stat-column.left-column {

				background: linear-gradient(135deg, #f0f8ff 0%, #e6f3ff 100%);

				border-color: #b3d9ff;

			}

			

			.stat-column.right-column {

				background: linear-gradient(135deg, #fff8f0 0%, #fff2e6 100%);

				border-color: #ffd9b3;

			}

			

			.stat-column:last-child {

				border-right: 1px solid #e0e0e0;

			}

			

			.stat-label {

				font-size: 12px;

				color: #333;

				padding: 8px 0 4px 0;

				background: transparent;

				border-bottom: none;

				font-weight: 600;

				margin-bottom: 2px;

			}
			
			.stat-column .stat-label:first-child {
				font-size: 14px;
				font-weight: 700;
				color: #000;
				padding: 0 0 8px 0;
				margin-bottom: 8px;
				border-bottom: 2px solid rgba(0,0,0,0.1);
				text-transform: uppercase;
				letter-spacing: 0.5px;
			}
			
			.stat-column.left-column .stat-label:first-child {
				color: #0066cc;
				border-bottom-color: #b3d9ff;
			}
			
			.stat-column.right-column .stat-label:first-child {
				color: #cc6600;
				border-bottom-color: #ffd9b3;
			}

			

			.stat-value {

				font-size: 14px;

				color: #000;

				padding: 0 0 8px 0;

				font-weight: 700;

				line-height: 1.3;

				margin-bottom: 8px;

			}
			
			.stat-column .stat-value:first-child {
				font-size: 16px;
				font-weight: 800;
				color: #000;
				margin-bottom: 12px;
			}
			
			/* Add subtle animations for better UX */
			.stat-column {
				animation: fadeInUp 0.6s ease-out;
			}
			
			.stat-column:nth-child(2) {
				animation-delay: 0.1s;
			}
			
			@keyframes fadeInUp {
				from {
					opacity: 0;
					transform: translateY(20px);
				}
				to {
					opacity: 1;
					transform: translateY(0);
				}
			}

			

			/* Responsive design */

			@media (max-width: 1200px) {

				.stat-column {

					min-width: 180px;

				}

				.stat-label {

					font-size: 11px;

					padding: 6px 0 3px 0;

				}

				.stat-value {

					font-size: 13px;

					padding: 0 0 6px 0;

				}

			}
			
			@media (max-width: 768px) {
				.stats-table {
					flex-direction: column;
					gap: 20px;
					align-items: center;
				}
				
				.stat-column {
					min-width: 280px;
					width: 100%;
					max-width: 320px;
				}
				
				.calculator-title {
					font-size: 24px;
				}
				
				.heading {
					padding: 20px;
				}
			}

			

			@media (max-width: 768px) {

				.header-row {

					flex-direction: column;

					align-items: flex-start;

					gap: 10px;

				}

				.stats-table {

					overflow-x: auto;

					width: 100%;

				}

			}



			#toolbar {

				background-color: #d1d3d4;

				padding: 10px;

				display: flex;

				align-items: center;

				justify-content: space-between;

			}



			#toolbar img {

				margin-right: 6px;

				width: 23px;

				height: 23px;

				cursor: pointer;

			}



			#toolbar img#tutorial {

				margin-right: 0;

			}



			#toolbar .btns .pos-relative {

				position: relative;

				display: inline-block;

			}



			#toolbar .btns ul.dropdown {

				list-style: none;

				padding-left: 0;

				margin: 0;

				z-index: 1;

				position: absolute;

				right: 0;

				background: #fff;

				border: 1px solid rgba(0, 0, 0, .15);

				border-radius: .25rem;

				box-shadow: 0px 0px 25px -11px rgba(0,0,0,0.75);

				display: none;

			}



			#toolbar .btns ul.dropdown li {

				padding: 10px;

				white-space: nowrap;

				cursor: pointer;

			}



			#toolbar .btns ul.dropdown li:hover {

				background-color: #e9ecef;

			}



			#toolbar .btns ul.dropdown li:first-child {

				border-bottom: 1px solid #c2c2c2;

			}



			/* Shapes dropdown styles - extends over canvas */

			#toolbar .shapes .shapes-dropdown {

				display: none;

				left: -33px;

				right: auto;

				position: fixed;

				top: 112px;

				z-index: 9999;

				max-height: 80vh;

				overflow-y: auto;

				/* background: white; */

				border: 1px solid #ddd;

				box-shadow: 0 2px 8px rgba(0,0,0,0.15);

				margin-top: 2px;

			}



			#toolbar .shapes .shapes-dropdown li {

				display: flex;

				align-items: center;

				justify-content: center;

				padding: 8px 12px;

				background: white;

				border-bottom: 1px solid #f0f0f0;

				cursor: pointer;

				font-size: 14px;

			}



			#toolbar .shapes .shapes-dropdown li:last-child {

				border-bottom: none;

			}



			#toolbar .shapes .shapes-dropdown li:hover {

				background-color: #f8f9fa;

			}



			#toolbar .shapes .shapes-dropdown li:last-child {

				border-bottom: none;

			}



			#toolbar .shapes .shapes-dropdown li img {

				width: 20px;

				height: 20px;

				margin: 0;

				object-fit: contain;

				display: block;

			}



			#toolbar .shapes .shapes-dropdown li span {

				font-size: 14px;

				color: #333;

			}



			#toolbar .shapes .shapes-dropdown li:hover {

				background-color: #f8f9fa;

			}



			/* Shapes dropdown button styles */

			#toolbar .shapes .shapes-dropdown-btn {

				display: flex;

				align-items: center;

				gap: 5px;

				cursor: pointer;

				padding: 5px;

				border-radius: 3px;

				position: relative;

			}



			#toolbar .shapes .shapes-dropdown-btn:hover {

				background-color: #e9ecef;

			}



			#toolbar .shapes .shapes-dropdown-btn .dropdown-arrow {

				font-size: 10px;

				color: #666;

				margin-left: 2px;

			}



			/* Active state for dropdown button when dropdown is open */

			#toolbar .shapes .shapes-dropdown-btn.active {

				background-color: #e9ecef;

			}



			#canvas-container {

				position: relative;

				overflow: visible; /* Allow modals to be visible */

				min-height: 600px;

				width: 100%;

			}

			

			/* Custom horizontal scrollbar */

			.horizontal-scrollbar {

				position: fixed;

				bottom: 10px; /* Bottom of the page/viewport */

				left: 50px; /* Account for ruler and some margin */

				height: 15px;

				background: #f1f1f1;

				border-radius: 7px;

				z-index: 1000;

				box-shadow: 0 2px 8px rgba(0,0,0,0.15);

			}

			

			.horizontal-scrollbar-thumb {

				position: absolute;

				top: 2px;

				left: 2px;

				height: 11px;

				background: #888;

				border-radius: 5px;

				cursor: pointer;

				transition: background 0.2s;

			}

			

			.horizontal-scrollbar-thumb:hover {

				background: #555;

			}

			

			.horizontal-scrollbar-thumb:active {

				background: #333;

			}

			

			/* Custom vertical scrollbar */

			.vertical-scrollbar {

				position: absolute;

				top: 30px; /* Account for ruler */

				right: 0;

				width: 15px;

				background: #f1f1f1;

				border-radius: 7px;

				z-index: 100;

			}

			

			.vertical-scrollbar-thumb {

				position: absolute;

				top: 2px;

				left: 2px;

				width: 11px;

				background: #888;

				border-radius: 5px;

				cursor: pointer;

				transition: background 0.2s;

			}

			

			.vertical-scrollbar-thumb:hover {

				background: #555;

			}

			

			.vertical-scrollbar-thumb:active {

				background: #333;

			}



			.ruler {

				position: absolute;

				background-color: #333333;

			}



			#ruler-x {

				top: 0;

				left: 0;

				height: 30px;

			}



			#ruler-y {

				top: 0;

				left: 0;

				width: 30px;

			}



			.ruler-text {

				position: absolute;

				font-size: 10px;

				color: #d1d3d4;



			}



			#ruler-y .ruler-text {

				rotate: 90deg;

			}



			#canvas {

				margin-left: 30px;

				margin-top: 30px;

				background-color: #fff;

			}



			#shapeModal,

			#emailModal,

			#videoTutorialModal {

				display: none;

				position: fixed;

				z-index: 1;

				left: 0;

				top: 0;

				width: 100%;

				height: 100%;

				padding-top: 20px;

				background-color: rgba(0, 0, 0, 0.5);

				justify-content: center;

				align-items: start;

			}



			#shapeModal .modal-content,

			#emailModal .modal-content,

			#videoTutorialModal .modal-content {

				background-color: white;

				padding: 20px;

				border-radius: 10px;

				width: 400px;

                max-height: 90vh;

                overflow-y: auto;

			}



			#shapeModal .modal-content .modal-title,

			#emailModal .modal-content .modal-title,

			#videoTutorialModal .modal-content .modal-title {

				margin-top: 0;

			}

			

			#videoTutorialModal .modal-content iframe {

				width: 100%;

				height: 350px;

			}



			#shapeModal .modal-content .form-group,

			#emailModal .modal-content .form-group {

				margin-bottom: 15px;

			}



			#shapeModal .modal-content .form-group label,

			#emailModal .modal-content .form-group label {

				display: block;

				padding-bottom: 0px;

			}



			#shapeModal .modal-content .form-group input,

			#emailModal .modal-content .form-group input {

				width: -webkit-fill-available;

				font-size: 15px;

				padding: 10px;

			}

			/* Auth Modal Styles */
			#authSection {
				display: none;
				position: fixed;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				z-index: 10000;
				width: 100%;
				height: 100%;
				background: rgba(0, 0, 0, 0.5);
				align-items: center;
				justify-content: center;
			}

			#authSection.show {
				display: flex;
			}

			.auth-container {
				background: white;
				border-radius: 15px;
				box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
				width: 100%;
				max-width: 600px;
				max-height: 85vh;
				overflow-y: auto;
				position: relative;
			}

			.auth-header {
				background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
				color: white;
				padding: 20px;
				text-align: center;
				border-radius: 15px 15px 0 0;
			}

			.auth-header h2 {
				margin: 0 0 10px 0;
				font-size: 24px;
				font-weight: 600;
				position: relative;
			}

			.auth-header .close-btn {
				position: absolute;
				top: -10px;
				right: -10px;
				background: rgba(255, 255, 255, 0.2);
				border: none;
				color: white;
				width: 30px;
				height: 30px;
				border-radius: 50%;
				cursor: pointer;
				font-size: 18px;
				display: flex;
				align-items: center;
				justify-content: center;
				transition: all 0.3s ease;
			}

			.auth-header .close-btn:hover {
				background: rgba(255, 255, 255, 0.3);
				transform: scale(1.1);
			}

			.auth-header p {
				margin: 0;
				opacity: 0.9;
				font-size: 12px;
			}

			.auth-tabs {
				display: flex;
				background: #f8f9fa;
				border-bottom: 1px solid #e9ecef;
			}

			.auth-tab {
				flex: 1;
				padding: 15px;
				text-align: center;
				cursor: pointer;
				transition: all 0.3s ease;
				border-bottom: 3px solid transparent;
			}

			.auth-tab.active {
				background: white;
				border-bottom-color: #667eea;
				color: #667eea;
				font-weight: 600;
			}

			.auth-tab:hover:not(.active) {
				background: #e9ecef;
			}

			.auth-form {
				display: none;
				padding: 20px;
				max-height: 60vh;
				overflow-y: auto;
			}

			.auth-form.active {
				display: block;
			}

			.auth-form .form-group {
				margin-bottom: 20px;
			}

			.auth-form label {
				display: block;
				margin-bottom: 8px;
				font-weight: 500;
				color: #495057;
			}

			.auth-form input {
				width: 100%;
				padding: 12px 15px;
				border: 2px solid #e9ecef;
				border-radius: 8px;
				font-size: 14px;
				transition: border-color 0.3s ease;
				box-sizing: border-box;
			}

			.auth-form input:focus {
				outline: none;
				border-color: #667eea;
				box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
			}

			.auth-btn {
				width: 100%;
				padding: 12px;
				border: none;
				border-radius: 8px;
				font-size: 16px;
				font-weight: 600;
				cursor: pointer;
				transition: all 0.3s ease;
				margin-bottom: 15px;
			}

			.auth-btn.primary {
				background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
				color: white;
			}

			.auth-btn.primary:hover {
				transform: translateY(-2px);
				box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
			}

			.auth-btn.secondary {
				background: #6c757d;
				color: white;
				margin-right: 10px;
				width: auto;
				display: inline-block;
			}

			.auth-btn.secondary:hover {
				background: #5a6268;
				transform: translateY(-2px);
				box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
			}

			.auth-error {
				background: #f8d7da;
				color: #721c24;
				padding: 10px;
				border-radius: 5px;
				margin-bottom: 15px;
				border: 1px solid #f5c6cb;
			}

			.auth-footer {
				padding: 20px 25px;
				text-align: center;
				border-top: 1px solid #e9ecef;
				background: #f8f9fa;
				border-radius: 0 0 15px 15px;
			}

			.auth-footer a {
				color: #667eea;
				text-decoration: none;
				font-weight: 500;
			}

			.auth-footer a:hover {
				text-decoration: underline;
			}



			/* Additional Auth Modal positioning and debugging */
			.auth-container {
				background: white;
			}

			/* Toolbar logout button styles */
			.toolbar-logout-btn {
				background: #dc3545 !important;
				color: white !important;
				border: none !important;
				padding: 6px 12px !important;
				border-radius: 4px !important;
				cursor: pointer !important;
				font-size: 12px !important;
				margin-left: 10px !important;
				transition: all 0.3s ease !important;
			}

			.toolbar-logout-btn:hover {
				background: #c82333 !important;
				transform: translateY(-1px) !important;
				box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3) !important;
			}

			/* Ensure calculator container has proper positioning */
			.calculator-container {
				position: relative;
				overflow: visible;
				background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
				/* padding: 20px; */
				border-radius: 10px;
				min-height: 100vh;
				font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
			}

			/* Email verification message styles */
			.verification-message {
				text-align: center;
				padding: 20px;
			}

			.verification-message h3 {
				color: #667eea;
				margin-bottom: 15px;
				font-size: 20px;
			}

			.verification-message p {
				color: #6c757d;
				margin-bottom: 15px;
				line-height: 1.5;
			}

			.verification-info {
				background: #f8f9fa;
				padding: 15px;
				border-radius: 8px;
				margin: 20px 0;
				text-align: left;
			}

			.verification-info p {
				margin: 8px 0;
				color: #495057;
			}

			.status-pending {
				color: #ffc107;
				font-weight: 600;
			}

			.verification-actions {
				margin-top: 20px;
			}

			.verification-actions .auth-btn {
				margin: 0 5px;
				width: auto;
				display: inline-block;
			}



			#shapeModal .form-group .edge-profile-section > div {

				display: none;

				padding: 30px;

				position: relative;

			}



			#shapeModal .form-group .edge-profile-section > div img {

				height: 100px;

				display: block;

				margin: 0 auto;

			}



            /* Enlarge U shape image and modal width when U is active */

            #shapeModal[data-shape="U"] .modal-content {

                width: 520px;

                max-height: 90vh;

                overflow-y: auto;

            }



            /* L shape: match U behavior */

            #shapeModal[data-shape="L"] .modal-content {

                width: 520px;

                max-height: 90vh;

                overflow-y: auto;

            }

            #shapeModal .form-group .edge-profile-section > div.L img {

                height: 400px;

            }

            #shapeModal[data-shape="L"] .form-group .edge-profile-section > div.L {

                padding: 0 10px;

                margin-top: -40px;

                margin-bottom: -10px;

            }

            #shapeModal .form-group .edge-profile-section > div.U img {

                height: 400px;

            }

            #shapeModal[data-shape="U"] .form-group .edge-profile-section > div.U {

                padding: 0 10px;

                margin-top: -40px;

                margin-bottom: -10px;

            }

            #shapeModal[data-shape="U"] .modal-content .form-group { 

                margin-bottom: 10px; 

            }

            /* (Reverted) keep default select width for U as before */



			#shapeModal .form-group .edge-profile-section > div select.shapeEdgeProfile {

				position: absolute;

				width: calc(400px - 260px);

			}



			/* For Square & Rounded Square */

			#shapeModal .form-group .edge-profile-section > div.square select#shapeEdgeProfile1,

			#shapeModal .form-group .edge-profile-section > div.rounded-square select#shapeEdgeProfile1 {

				top: 0;

				left: 50%;

				transform: translateX(-50%);

			}



			#shapeModal .form-group .edge-profile-section > div.square select#shapeEdgeProfile2,

			#shapeModal .form-group .edge-profile-section > div.rounded-square select#shapeEdgeProfile2 {

				right: 0;

				top: 50%;

				transform: translateY(-50%);

			}



			#shapeModal .form-group .edge-profile-section > div.square select#shapeEdgeProfile3,

			#shapeModal .form-group .edge-profile-section > div.rounded-square select#shapeEdgeProfile3 {

				bottom: 0;

				left: 50%;

				transform: translateX(-50%);

			}



			#shapeModal .form-group .edge-profile-section > div.square select#shapeEdgeProfile4,

			#shapeModal .form-group .edge-profile-section > div.rounded-square select#shapeEdgeProfile4 {

				left: 0;

				bottom: 40%;

				transform: translateY(-50%);

			}        



			/* For Circle & Ellipse */

			#shapeModal .form-group .edge-profile-section > div.ellipse select#shapeEdgeProfile1,

			#shapeModal .form-group .edge-profile-section > div.circle select#shapeEdgeProfile1 {

				top: 0;

				left: 50%;

				transform: translateX(-50%);

			} 



			/* For Polygon */

			#shapeModal .form-group .edge-profile-section > div.polygon select#shapeEdgeProfile1 {

				top: 0;

				left: 50%;

				transform: translateX(-50%);

			}



			#shapeModal .form-group .edge-profile-section > div.polygon select#shapeEdgeProfile2 {

				right: 0;

				top: 35%;

				transform: translateY(-50%);

			}



			#shapeModal .form-group .edge-profile-section > div.polygon select#shapeEdgeProfile3 {

				right: 0;

				top: 65%;

				transform: translateY(-50%);

			}



			#shapeModal .form-group .edge-profile-section > div.polygon select#shapeEdgeProfile4 {

				bottom: 0;

				left: 50%;

				transform: translateX(-50%);

			}



			#shapeModal .form-group .edge-profile-section > div.polygon select#shapeEdgeProfile5 {

				left: 0;

				bottom: 25%;

				transform: translateY(-50%);

			} 



			#shapeModal .form-group .edge-profile-section > div.polygon select#shapeEdgeProfile6 {

				left: 0;

				bottom: 55%;

				transform: translateY(-50%);

			}



			/* For Triangle */

			#shapeModal .form-group .edge-profile-section > div.triangle select#shapeEdgeProfile1 {

				right: 0;

				top: 50%;

				transform: translateY(-50%);

			}



			#shapeModal .form-group .edge-profile-section > div.triangle select#shapeEdgeProfile2 {

				bottom: 0;

				left: 50%;

				transform: translateX(-50%);

			}



			#shapeModal .form-group .edge-profile-section > div.triangle select#shapeEdgeProfile3 {

				left: 0;

				bottom: 40%;

				transform: translateY(-50%);

			}



			/* L Shape Edge Profiles */

			#shapeModal .form-group .edge-profile-section > div.L select#shapeEdgeProfile1 {

				top: 60px;

				left: 50%;

				transform: translateX(-50%);

			}

			#shapeModal .form-group .edge-profile-section > div.L select#shapeEdgeProfile2 {

				right: -25px;

				top: 37%;

				transform: translateY(-50%);

			}

			#shapeModal .form-group .edge-profile-section > div.L select#shapeEdgeProfile3 {

				right: 110px;

				top: 54%;

				transform: translateY(-50%);

			}

			#shapeModal .form-group .edge-profile-section > div.L select#shapeEdgeProfile4 {

				bottom: 122px;

				left: 57%;

				transform: translateX(-50%);

			}

			#shapeModal .form-group .edge-profile-section > div.L select#shapeEdgeProfile5 {

				bottom: 76px;

				left: 33%;

				transform: translateX(-50%);

			}

			#shapeModal .form-group .edge-profile-section > div.L select#shapeEdgeProfile6 {

				left: -24px;

				bottom: 55%;

				transform: translateY(-50%);

			}



			/* U Shape Edge Profiles */

			#shapeModal .form-group .edge-profile-section > div.U select#shapeEdgeProfile1 {

				top: 70px;

				left: 50%;

				transform: translateX(-50%);

			}

			#shapeModal .form-group .edge-profile-section > div.U select#shapeEdgeProfile2 {

				top: 35%;

				left: 90%;

				transform: translateX(-50%);

			}

			#shapeModal .form-group .edge-profile-section > div.U select#shapeEdgeProfile3 {

				bottom: 80px;

				left: 70%;

				transform: translateX(-50%);

			}

			#shapeModal .form-group .edge-profile-section > div.U select#shapeEdgeProfile4 {

				bottom: 80px;

				left: 30%;

				transform: translateX(-50%);

			}

			#shapeModal .form-group .edge-profile-section > div.U select#shapeEdgeProfile5 {

				top: 35%;

				left: 11%;

				transform: translateX(-50%);

			}

			#shapeModal .form-group .edge-profile-section > div.U select#shapeEdgeProfile6 {

				top: 35%;

				left: 50%;

				transform: translateX(-50%);

			}



			/* Shape-3 Edge Profiles */

			#shapeModal .form-group .edge-profile-section > div.shape-3 select#shapeEdgeProfile1 {

				top: 0;

				left: 50%;

				transform: translateX(-50%);

			}

			#shapeModal .form-group .edge-profile-section > div.shape-3 select#shapeEdgeProfile2 {

				right: 0;

				top: 50%;

				transform: translateY(-50%);

			}

			#shapeModal .form-group .edge-profile-section > div.shape-3 select#shapeEdgeProfile3 {

				bottom: 0;

				left: 50%;

				transform: translateX(-50%);

			}

			#shapeModal .form-group .edge-profile-section > div.shape-3 select#shapeEdgeProfile4 {

				left: 0;

				top: 50%;

				transform: translateY(-50%);

			}





			#shapeModal .modal-content button,

			#emailModal .modal-content button,

			#videoTutorialModal .modal-content button {

				display: inline-block;

				color: #fff;

				font-weight: 400;

				line-height: 1.5;

				text-align: center;

				text-decoration: none;

				vertical-align: middle;

				cursor: pointer;

				-webkit-user-select: none;

				-moz-user-select: none;

				user-select: none;

				padding: .375rem .75rem;

				font-size: 1rem;

				border-radius: .25rem;

				cursor: pointer;

				float: right;

				transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;

			}



			#shapeModal .modal-content button#saveShapeDetails,

			#emailModal .modal-content button[type="submit"] {

				background-color: #0d6efd;

				border: 1px solid #0d6efd;

			}



			#shapeModal .modal-content button#cancel,

			#emailModal .modal-content button#cancel,

			#videoTutorialModal .modal-content button#cancel {

				background-color: #6c757d;

				border: 1px solid #6c757d;

				margin-right: 10px;

			}

			

			#videoTutorialModal .modal-content button#cancel {

				margin-top: 10px;

				margin-right: 0;

			}



			#shapeModal .modal-content button#saveShapeDetails:hover,

			#emailModal .modal-content button[type="submit"]:hover {

				color: #fff;

				background-color: #0b5ed7;

				border-color: #0a58ca;

			}



			#shapeModal .modal-content button#cancel:hover,

			#emailModal .modal-content button#cancel:hover,

			#videoTutorialModal .modal-content button#cancel:hover {

				color: #fff;

				background-color: #5c636a;

				border-color: #565e64;

			}



			#shapeModal .modal-content button#saveShapeDetails:active,

			#emailModal .modal-content button[type="submit"]:active {

				color: #fff;

				background-color: #0a58ca;

				border-color: #0a53be;

			}



			#shapeModal .modal-content button#cancel:active,

			#emailModal .modal-content button#cancel:active,

			#videoTutorialModal .modal-content button#cancel:active {

				color: #fff;

				background-color: #565e64;

				border-color: #51585e;

			}



			#shapeModal .modal-content button#saveShapeDetails:focus,

			#emailModal .modal-content button[type="submit"]:focus {

				color: #fff;

				background-color: #0b5ed7;

				border-color: #0a58ca;

				box-shadow: 0 0 0 .25rem rgba(49,132,253,.5);

			}



			#shapeModal .modal-content button#cancel:focus,

			#emailModal .modal-content button#cancel:focus,

			#videoTutorialModal .modal-content button#cancel:focus {

				color: #fff;

				background-color: #5c636a;

				border-color: #565e64;

				box-shadow: 0 0 0 .25rem rgba(130,138,145,.5);

			}

			

			

			@media only screen and (max-width: 1100px) and (min-width: 800px) {

				#toolbar img {

					margin-right: 8px;

					width: 25px;

					height: 25px;

				}

			}

			

			@media only screen and (min-width: 1101px) {

				#toolbar img {

					margin-right: 15px;

					width: 30px;

					height: 30px;

				}

			}

			

			/* Native Browser Fullscreen styles */

			.fullscreen-mode {

				width: 100vw !important;

				height: 100vh !important;

				background: white !important;

				margin: 0 !important;

				padding: 0 !important;

			}

			

			.fullscreen-mode .heading {

				height: 60px !important;

				display: flex !important;

				background: white !important;

				position: relative !important;

				margin: 0 !important;

				padding: 0 10px !important;

				border-bottom: 1px solid #ddd !important;

			}

			

			.fullscreen-mode #toolbar {

				height: 60px !important;

				position: relative !important;

				background-color: #d1d3d4 !important;

				margin: 0 !important;

				border-bottom: 1px solid #aaa !important;

			}

			

			.fullscreen-mode #canvas-container {

				height: calc(100vh - 120px) !important;

				width: 100vw !important;

				overflow: hidden !important;

				position: relative !important;

				background: white !important;

				margin: 0 !important;

				padding: 0 !important;

			}

			

			/* Fullscreen canvas sizing */

			.fullscreen-mode #canvas {

				max-width: calc(100vw - 30px) !important;

				max-height: calc(100vh - 150px) !important;

			}

			

			/* Fullscreen scrollbar styles */

			.fullscreen-mode .horizontal-scrollbar {

				left: 50px !important;

				bottom: 5px !important;

				width: calc(100vw - 100px) !important;

			}

			

			.fullscreen-mode .vertical-scrollbar {

				top: 30px !important;

				height: calc(100vh - 135px) !important;

			}

			

			/* When browser is in fullscreen mode */

			:-webkit-full-screen .fullscreen-mode {

				width: 100vw !important;

				height: 100vh !important;

			}

			

			:-moz-full-screen .fullscreen-mode {

				width: 100vw !important;

				height: 100vh !important;

			}

			

			:fullscreen .fullscreen-mode {

				width: 100vw !important;

				height: 100vh !important;

			}

			

			/* Modal styles in fullscreen */

			.fullscreen-mode .modal {

				z-index: 1000001 !important;

				position: fixed !important;

			}

			

			/* Prevent fullscreen exit when modals open */

			.modal {

				pointer-events: auto !important;

			}

			

			/* Fullscreen icon states */

			#fullscreen.exit-fullscreen {

				opacity: 0.7;

				transform: rotate(180deg);

				transition: all 0.3s ease;

			}

			

			#fullscreen:hover {

				opacity: 1;

				transform: scale(1.1);

				transition: all 0.3s ease;

			}

			

			/* Disabled state for toolbar and canvas */
			#toolbar.disabled,
			#canvas.disabled {
				opacity: 0.5;
				pointer-events: none;
			}



			#toolbar.disabled img,
			#toolbar.disabled .shapes-dropdown-btn,
			#toolbar.disabled .btns .pos-relative {
				cursor: not-allowed;
			}

			#canvas.disabled {
				cursor: not-allowed;
			}

			/* Hide shape-specific toolbar icons by default - they will be shown via JavaScript when shapes are selected */
			#toolbar .tools #info,
			#toolbar .tools #clone,
			#toolbar .tools #delete,
			#toolbar .tools #rotate {
				display: none;
			}
			
			/* Saved Drawings Grid Styles */
			.drawings-grid {
				display: grid;
				grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
				gap: 20px;
				margin: 20px 0;
			}
			
			.drawing-item {
				border: 1px solid #ddd;
				border-radius: 8px;
				padding: 15px;
				background: #fff;
				box-shadow: 0 2px 4px rgba(0,0,0,0.1);
			}
			
			.drawing-item h4 {
				margin: 0 0 10px 0;
				color: #333;
				border-bottom: 2px solid #007cba;
				padding-bottom: 5px;
			}
			
			.drawing-item p {
				margin: 5px 0;
				color: #666;
			}
			
			.drawing-actions {
				margin-top: 15px;
				padding-top: 15px;
				border-top: 1px solid #eee;
			}
			
			.drawing-actions .button {
				margin-right: 10px;
				margin-bottom: 5px;
				padding: 8px 12px;
				text-decoration: none;
				border-radius: 4px;
				font-size: 12px;
			}
			
			.button-delete {
				background-color: #dc3545;
				color: white;
				border: none;
				cursor: pointer;
			}
			
			.button-delete:hover {
				background-color: #c82333;
			}
			
			/* Drawings Grid Styling */
			.drawings-grid {
				display: grid;
				grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
				gap: 20px;
				margin-top: 20px;
			}
			
			.drawing-item {
				background: white;
				border: 1px solid #e0e0e0;
				border-radius: 8px;
				padding: 20px;
				box-shadow: 0 2px 8px rgba(0,0,0,0.1);
			}
			
			.drawing-item h4 {
				margin: 0 0 15px 0;
				color: #333;
				font-size: 18px;
			}
			
			.drawing-item p {
				margin: 8px 0;
				color: #666;
				font-size: 14px;
			}
			
			.drawing-item p strong {
				color: #333;
			}
			
			.drawing-actions {
				margin-top: 20px;
				display: flex;
				gap: 10px;
				flex-wrap: wrap;
			}
			
			.drawing-actions .button {
				padding: 8px 16px;
				font-size: 14px;
				text-decoration: none;
				border-radius: 4px;
				border: none;
				cursor: pointer;
				transition: background-color 0.3s;
			}
			
			.drawing-actions .button:not(.button-delete) {
				background-color: #007cba;
				color: white;
			}
			
			.drawing-actions .button:not(.button-delete):hover {
				background-color: #005a87;
			}
		</style>

	</head>

	<body>

		<div class="calculator-container">

							<div style="font-family: Arial, sans-serif; padding: 10px; background-color: #f0f0f0;">

					<div class="header-row">

						<div  style="
        display: flex;
        justify-content: space-between;
        color: #333;
    ">

							 <div style="
            font-weight: bold;
            line-height: 12px;
			 font-size:18px;
			 padding-top:18px;
        ">
            "<?=$_GET['name']?>" Slab & Cutting Area  MM Calculator
        </div>

							 <div style="
            text-align: center;
            flex: 1;
            line-height: 12px;
			font-size:14px;
			padding-left:30px;
        ">
            <div><strong>Total Slab:</strong> <span id="slabUsageDisplay" style="color: #0066cc;">1 Slab/s</span></div>
            <div><strong>Only Cut Area:</strong> <span class="only_cut_mm" style="color: #666;">0</span> mm <span style="color: #888; font-size: 8px;">(50.00 p/mm)</span></div>
            <div><strong>Mitred Cut Area:</strong> <span class="mitred_edge_mm" style="color: #666;">0</span> mm <span style="color: #888; font-size: 8px;">(50.16 p/mm)</span></div>
            <div><strong>Total Cutting:</strong> <span id="totalCuttingDisplay" style="color: #cc6600;">0 mm</span></div>
        </div>
        
        <!-- Right: Cost Stats -->
        <div style="
            text-align: right;
            flex: 1;
			 font-size:14px;
            line-height: 12px;
        ">
            <div><strong>Slab Cost:</strong> <span id="slabCostDisplay" style="color: #0066cc; font-weight: bold;">$1000</span></div>
            <div><strong>Production Cost:</strong> <span id="productionCostDisplay" style="color: #666;">$0</span></div>
            <div><strong>Installation Cost:</strong> <span id="installationCostDisplay" style="color: #666;">$0</span></div>
            <div><strong>Total Project Cost:</strong> <span id="totalProjectCostDisplay" style="color: #cc0000; font-weight: bold;">$1000</span></div>
        </div>
						</div>



					<!-- <div class="stats-table">

						<div class="stat-column">

							<div class="stat-label">Total Slabs:</div>

							<div class="stat-value">1 Slabs</div>

						</div>

						<div class="stat-column">

							<div class="stat-label">Slab Cost:</div>

							<div class="stat-value">$1000</div>

						</div>

						<div class="stat-column">

							<div class="stat-label">Only Cut Area:</div>

							<div class="stat-value"><span class="only_cut_mm">0</span> mm (in metres)</div>

						</div>

						<div class="stat-column">

							<div class="stat-label">Installation Cost:</div>

							<div class="stat-value">$0</div>

						</div>

						<div class="stat-column">

							<div class="stat-label">Mitred Cut Area:</div>

							<div class="stat-value"><span class="mitred_edge_mm">0</span> mm (in metres)</div>

						</div>

						<div class="stat-column">

							<div class="stat-label">Total Project Cost:</div>

							<div class="stat-value">$1000</div>

						</div>

					</div> -->

				</div>

			</div>

			<div id="toolbar" class="disabled">

				<div class="shapes">

					<div class="pos-relative">

						<div class="shapes-dropdown-btn">

							<img src="./../assets/images/square.png" alt="Shapes">

							<span class="dropdown-arrow">▼</span>

						</div>

						<ul class="dropdown shapes-dropdown">

							<li data-shape="square">

								<img src="./../assets/images/square.png" alt="Square">

							</li>

							<li data-shape="rounded-square">

								<img src="./../assets/images/rounded-square.png" alt="Rounded Square">

							</li>

							<li data-shape="circle">

								<img src="./../assets/images/circle.png" alt="Circle">

							</li>

							<li data-shape="ellipse">

								<img src="./../assets/images/ellipse.png" alt="Ellipse">

							</li>

							<li data-shape="polygon">

								<img src="./../assets/images/polygon.png" alt="Polygon">

							</li>

							<li data-shape="triangle">

								<img src="./../assets/images/triangle.png" alt="Triangle">

							</li>

							<li data-shape="L">

								<img src="./../assets/images/L.png" alt="L" style="width: 29px !important; height: 29px !important;">

							</li>

							<li data-shape="U">

								<img src="./../assets/images/U.png" alt="U" style="width: 30px !important; height: 30px !important;">

							</li>

							<li data-shape="shape-3">

								<img src="./../assets/images/shape-3.png" alt="Shape 3" style="width: 30px !important; height: 30px !important;">

							</li>

						</ul>

					</div>

				</div>

				<div class="tools">

					<img src="./../assets/images/info.png" alt="Info" id="info">

					<img src="./../assets/images/shape-3.png" alt="Clone" title="Clone Shape" id="clone">

					<img src="./../assets/images/rotate.png" alt="Rotate" id="rotate">

					<img src="./../assets/images/zoom-in.png" alt="Zoom In" id="zoom-in">

					<img src="./../assets/images/zoom-out.png" alt="Zoom Out" id="zoom-out">

					<img src="./../assets/images/undo.png" alt="Undo" title="Undo" id="undo">

					<img src="./../assets/images/redo.png" alt="Redo" title="Redo" id="redo">

					<img src="./../assets/images/delete.png" alt="Delete" id="delete">

					<img src="./../assets/images/fullscreen.png" alt="Fullscreen" title="Toggle Fullscreen" id="fullscreen">

				</div>

				<div class="btns">

					<div class="pos-relative">

						<img src="./../assets/images/download.png" alt="Download" id="download">

						<ul class="dropdown">

							<li data-type="PDF">Download as PDF</li>

							<li data-type="EMAIL">Send Email (Registered Users Only)</li>
<li data-type="SAVE">Save Drawing</li>
<li data-type="VIEW">View Saved Drawings</li>
						</ul>

					</div>

					<!-- Logout button - only visible when authenticated -->
					<button id="toolbarLogoutBtn" class="toolbar-logout-btn" style="display: none;">
						Logout
					</button>

					<img src="./../assets/images/tutorial.png" alt="Tutorial" id="tutorial">

				</div>



			</div>



			<div id="canvas-container">
					
			 <div id="authSection" class="auth-section">
					<div class="auth-container">
						<div class="auth-header">
							<h2>Welcome 
								<button class="close-btn" id="closeAuthModal">&times;</button>
							</h2>
							<p>Please login or register to access the calculator</p>
						</div>
						
						<div class="auth-tabs">
							<div class="auth-tab active" id="loginTab">Login</div>
							<div class="auth-tab" id="registerTab">Register</div>
						</div>
						
					
						 						<form id="loginForm" class="auth-form active" onsubmit="return false;">
							<div class="form-group">
								<label for="email">Email</label>
								<input type="email" id="email" name="email" placeholder="Enter your email" required>
							</div>
							<div class="form-group">
								<label for="password">Password</label>
								<input type="password" id="password" name="password" placeholder="Enter password" required>
							</div>
							<div class="auth-error" id="loginError" style="display: none;"></div>
							<button type="submit" class="auth-btn primary">Login</button>
						</form>
						 
						<!-- Register Form -->
						<form id="registerForm" class="auth-form">
							<div class="form-group">
								<label for="reg_username">Username</label>
								<input type="text" id="reg_username" name="reg_username" required>
							</div>
							<div class="form-group">
								<label for="reg_email">Email</label>
								<input type="email" id="reg_email" name="reg_email" required>
							</div>
							<div class="form-group">
								<label for="reg_password">Password</label>
								<input type="password" id="reg_password" name="reg_password" required>
							</div>
							<div class="form-group">
								<label for="reg_confirm_password">Confirm Password</label>
								<input type="password" id="reg_confirm_password" name="reg_confirm_password" required>
							</div>
							<div class="auth-error" id="registerError" style="display: none;"></div>
							<button type="submit" class="auth-btn primary">Register</button>
						</form>

						<!-- Email Verification Message - TEMPORARILY DISABLED -->
						<div id="emailVerificationMessage" class="auth-form" style="display: none;">
							<div class="verification-message">
								<h3>Check Your Email</h3>
								<p>We've sent a verification link to your email address. Please check your inbox and click the verification link to activate your account.</p>
								<div class="verification-info">
									<p><strong>Email:</strong> <span id="verificationEmail"></span></p>
									<p><strong>Status:</strong> <span class="status-pending">Pending Verification</span></p>
								</div>
								<div class="verification-actions">
									<button type="button" class="auth-btn secondary" id="resendVerificationBtn">Resend Verification</button>
									<button type="button" class="auth-btn secondary" id="backToLoginBtn">Back to Login</button>
								</div>
							</div>
						</div> 
					
													<div class="auth-footer">
								<div id="authFooterText">
									Not a member yet? <a href="#" id="switchToRegister">Register now.</a>
								</div>
								<div id="logoutSection" style="display: none; margin-top: 15px; text-align: center;">
									<button type="button" id="logoutBtn" class="auth-btn" style="background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer;">
										Logout
									</button>
								</div>
							</div>
					</div>
				</div> 

				<div class="ruler" id="ruler-x"></div>

				<div class="ruler" id="ruler-y"></div>

				<canvas id="canvas" class="disabled"></canvas>

				

				<!-- Custom scrollbars -->

				<div class="horizontal-scrollbar" id="horizontal-scrollbar">

					<div class="horizontal-scrollbar-thumb" id="horizontal-scrollbar-thumb"></div>

				</div>

				<div class="vertical-scrollbar" id="vertical-scrollbar">

					<div class="vertical-scrollbar-thumb" id="vertical-scrollbar-thumb"></div>

				</div>

			</div>

		</div>



		<!-- Modal structure -->

		<div id="shapeModal" class="modal">

			<div class="modal-content">

				<h2 class="modal-title">Shape Details</h2>



				<div class="form-group">

					<label for="shapeName">Shape Name</label>

					<input type="text" id="shapeName" name="shapeName" placeholder="Enter Name">

				</div>



				<div class="form-group">

					<label for="shapeWidth">Width/Length (mm)</label>

					<input type="number" id="shapeWidth" min="0" name="shapeWidth">

				</div>



				<div class="form-group">

					<label for="shapeHeight">Height/Depth (mm)</label>

					<input type="number" id="shapeHeight" min="0" name="shapeHeight">

				</div>



				<div class="form-group">

					<label for="shapeEdgeProfile">Edge Profiles</label>

					<div class="edge-profile-section">

						<div class="square" data-count="4">

							<img src="./../assets/images/square.png" alt="Square" data-shape="square">

						</div>

						<div class="rounded-square" data-count="4">

							<img src="./../assets/images/rounded-square.png" alt="Rounded Square" data-shape="rounded-square">

						</div>

						<div class="circle" data-count="1">

							<img src="./../assets/images/circle.png" alt="Circle" data-shape="circle">

						</div>

						<div class="ellipse" data-count="1">

							<img src="./../assets/images/ellipse.png" alt="Ellipse" data-shape="ellipse">

						</div>

						<div class="polygon" data-count="6">

							<img src="./../assets/images/polygon.png" alt="Polygon" data-shape="polygon">

						</div>

						<div class="triangle" data-count="3">

							<img src="./../assets/images/triangle.png" alt="Triangle" data-shape="triangle">

						</div>

						<div class="L" data-count="6">

							<img src="./../assets/images/L.png" alt="L" data-shape="L">

						</div>

						<div class="U" data-count="6">

							<img src="./../assets/images/U.png" alt="U" data-shape="U">

						</div>

						<div class="shape-3" data-count="4">

							<img src="./../assets/images/shape-3.png" alt="Shape 3" data-shape="shape-3">

						</div>

					</div>

				</div>



				<button type="button" id="saveShapeDetails" data-state="save">Draw</button>

				<button type="button" id="cancel">Cancel</button>

			</div>

		</div>

		

		<!-- Video Tutorial Modal -->

		<div id="videoTutorialModal" class="modal">

			<div class="modal-content">

				<h2 class="modal-title">Tutorial Video</h2>



				<iframe frameborder="0" allowfullscreen></iframe>



				<button type="button" id="cancel">Close</button>

			</div>

		</div>

		

		<!-- Email Modal -->
		<div id="emailModal" class="modal">

			<div class="modal-content">

				<h2 class="modal-title">Send Email</h2>



				<form id="email-form">

					<div class="form-group">

						<label for="email">Enter Registered Email Address:</label>
						<input type="email" id="email" name="email" required placeholder="Enter your registered email address">
						<small style="color: #666; font-size: 0.9em; margin-top: 5px; display: block;">
							Note: Email can only be sent to registered user accounts. Please ensure you're using the email address associated with your account.
						</small>
					</div>

					

					<div class="error-message" style="color:red;margin: 10px 0;display:none;"></div>

					

					<button type="submit">Send</button>

				</form>

				

				<button type="button" id="cancel">Close</button>

			</div>

		</div>

		<!-- Save Drawing Modal -->
		<div id="saveDrawingModal" class="modal">
			<div class="modal-content">
				<h2 class="modal-title">Save Drawing</h2>
				<form id="save-drawing-form">
					<div class="form-group">
						<label for="drawing-name">Drawing Name:</label>
						<input type="text" id="drawing-name" name="drawing-name" required placeholder="Enter a name for this drawing">
					</div>
					<div class="form-group">
						<label for="drawing-notes">Notes (Optional):</label>
						<textarea id="drawing-notes" name="drawing-notes" placeholder="Add any notes about this drawing"></textarea>
					</div>
					<div class="error-message" style="color:red;margin: 10px 0;display:none;"></div>
					<button type="submit">Save Drawing</button>
				</form>
				<button type="button" id="cancel-save">Close</button>
			</div>
		</div>

		<!-- View Saved Drawings Modal -->
		<div id="viewDrawingsModal" class="modal">
			<div class="modal-content">
				<h2 class="modal-title">Saved Drawings</h2>
				<div id="saved-drawings-list">
					<p>Loading your saved drawings...</p>
				</div>
				<button type="button" id="cancel-view">Close</button>
			</div>
		</div>

		<script src="./../assets/js/jquery-3.6.0.min.js"></script>

		<script src="./../assets/js/fabric.min.js"></script>

		<script src="./../assets/js/jspdf.umd.min.js"></script>

		<script>
			// Define AJAX URL for authentication
			// Get the WordPress site URL from URL parameters to construct the correct AJAX URL
			var urlParams = new URLSearchParams(window.location.search);
			var siteUrl = urlParams.get('site_url') || window.location.protocol + '//' + window.location.hostname;
			var ajaxurl = siteUrl + '/wp-admin/admin-ajax.php';
			// Get nonce from URL parameters
			var nonce = urlParams.get('nonce') || 'disabled_for_testing';
			
			var stone_slab_ajax = {
				ajaxurl: ajaxurl,
				nonce: nonce
			};

			jQuery(document).ready(function() {
				// Debug: Check if auth button exists
				console.log('Document ready - checking auth button');
				console.log('Auth button element:', jQuery('#auth'));
				console.log('Auth button in toolbar .btns:', jQuery('#toolbar .btns #auth'));
				console.log('Toolbar disabled state:', jQuery('#toolbar').hasClass('disabled'));

				// Test button for Slab Usage Feature
				jQuery('#testSlabUsage').click(function() {
					console.log('Test Slab Usage button clicked!');
					
					// Test the calculateSlabUsage function
					if (typeof calculateSlabUsage === 'function') {
						console.log('calculateSlabUsage function exists');
						calculateSlabUsage();
					} else {
						console.log('calculateSlabUsage function NOT found');
					}
					
					// Test the updateSlabVisualization function
					if (typeof updateSlabUsage === 'function') {
						console.log('updateSlabVisualization function exists');
						updateSlabVisualization();
					} else {
						console.log('updateSlabVisualization function NOT found');
					}
					
					// Show current totalMM value
					console.log('Current totalMM:', totalMM);
					
					// Show current slab dimensions
					console.log('Slab width (mm):', <?=$_GET['slab_width']?>);
					console.log('Slab height (mm):', <?=$_GET['slab_height']?>);
					
					alert('Check browser console for test results!');
				});
				
				// Initialize display values
				function initializeDisplayValues() {
					// Set initial values
					jQuery('#slabUsageDisplay').html('1 Slab/s');
					jQuery('#totalCuttingDisplay').html('0 mm');
					jQuery('#slabCostDisplay').html('$1000');
					jQuery('#productionCostDisplay').html('$0.00');
					jQuery('#installationCostDisplay').html('$0.00');
					jQuery('#totalProjectCostDisplay').html('$1000.00');
					
					// Update the cutting area displays with proper formatting
					jQuery('.only_cut_mm').html('0');
					jQuery('.mitred_edge_mm').html('0');
					
					console.log('Display values initialized');
				}
				
				// Call initialization function
				initializeDisplayValues();

				// Initialize toolbar logout button as hidden
				jQuery('#toolbarLogoutBtn').hide();

				// Global function to force hide auth modal
				function forceHideAuthModal() {
					console.log('Force hiding auth modal');
					jQuery('#authSection').removeClass('show');
					jQuery('#authSection').hide();
					jQuery('#authSection').css('display', 'none');
					jQuery('#authSection').attr('style', 'display: none !important');
				}

				// Ensure shape-specific toolbar icons are hidden initially
				hideShapeToolbarIcons();

				// Ensure auth modal is hidden initially
				forceHideAuthModal();

				// Functions to enable/disable toolbar and canvas
				function enableCalculator() {
					console.log('enableCalculator() called, isAuthenticated:', isAuthenticated);
					// Check if user is authenticated before enabling
					if (isAuthenticated) {
						console.log('User is authenticated, enabling calculator');
						jQuery('#toolbar').removeClass('disabled');
						jQuery('#canvas').removeClass('disabled');
						console.log('Hiding auth modal in enableCalculator');
						// Use the global function to force hide the auth modal
						forceHideAuthModal();
						// Show toolbar logout button
						jQuery('#toolbarLogoutBtn').show();
						console.log('Calculator enabled for authenticated user');
					} else {
						// User not authenticated, show auth modal
						console.log('User not authenticated, showing auth modal');
						jQuery('#authSection').addClass('show').css('display', 'flex');
					}
				}

				function disableCalculator() {
					jQuery('#toolbar').addClass('disabled');
					jQuery('#canvas').addClass('disabled');
					jQuery('#authSection').addClass('show');
					// Hide toolbar logout button
					jQuery('#toolbarLogoutBtn').hide();
				}

				// Convert mm to pixels (adjust this factor based on your canvas scaling)

				function convertMmToPx(value) {

					return value / 8;

				}

				

				function convertPxToMm(value) {

					return value * 8;

				}



				// Initial canvas and ruler setup - dynamic sizing

				let canvasWidth = window.innerWidth - 30; // Start with full viewport width

				let canvasHeight = convertMmToPx(<?=$_GET['pad_height']?>); // Keep original height

				const rulerInterval = 100; // 50mm intervals

				let zoomLevel = 1.6; // Initial zoom level

				let shapeNo = 0; // Shape Number on Canvas

				

				let lastMoveTime = Date.now();



				// Defalt Shapes Sizes

				const defaultShapeWidth = 1500;

				const defaultShapeHeight = 700;

				

				let totalMM = 0;

				let onlyCutAreaMM = 0;

				let mitredEdgeAreaMM = 0;



				const defaultGap = convertMmToPx(20); // 20mm gap in pixels



				// Box properties

				const boxWidth = convertMmToPx(<?=$_GET['slab_width']?>);

				const boxHeight = convertMmToPx(<?=$_GET['slab_height']?>);



				const infoBoxes = [];



				// Keep track of the initial box

				let initialBox = null;





				var canvas = canvas = new fabric.Canvas('canvas', {

					width: canvasWidth,

					height: canvasHeight,

					backgroundColor: 'white',

// 					allowTouchScrolling: true,

				});


				

				canvas.selection = false; // disable group selection

				

				fabric.Object.prototype.objectCaching = true;

				// Hide shape-specific toolbar icons initially
				hideShapeToolbarIcons();

				

				// Dynamic canvas expansion function

				function expandCanvasIfNeeded() {

					let maxRight = 0;

					let maxBottom = 0;

					

					// Find the furthest bounds of all objects

					canvas.getObjects().forEach(obj => {

						if (!obj.isWatermark && !isGreenBox(obj)) {

							const bounds = obj.getBoundingRect();

							maxRight = Math.max(maxRight, bounds.left + bounds.width + 100); // 100px padding

							maxBottom = Math.max(maxBottom, bounds.top + bounds.height + 100); // 100px padding

						}

					});

					

					// Expand canvas if needed

					let needsUpdate = false;

					if (maxRight > canvas.getWidth()) {

						canvas.setWidth(maxRight);

						needsUpdate = true;

					}

					if (maxBottom > canvas.getHeight()) {

						canvas.setHeight(maxBottom);

						needsUpdate = true;

					}

					

					// Update rulers and scrollbars if canvas size changed

					if (needsUpdate) {

						addRulers();

						updateScrollbars();

						canvas.renderAll();

					}

				}

				

				// Watermark functionality

				let watermarkPattern = null;

				

				function loadWatermarkForBox(greenBox) {

					fabric.Image.fromURL('./../assets/images/watermark.png', function(img) {

						// Center watermark in the specified green box

						const centerX = greenBox.left + (greenBox.width / 2);

						const centerY = greenBox.top + (greenBox.height / 2);

						

						// Scale down the watermark size (15% of original)

						const scale = 0.15;

						

						// Set watermark properties - single image, reduced size

						img.set({

							left: centerX,

							top: centerY,

							opacity: 0.15, // Reduced opacity to make watermark lighter

							selectable: false,

							evented: false,

							excludeFromExport: false,

							isWatermark: true,

							originX: 'center',

							originY: 'center',

							scaleX: scale,

							scaleY: scale

						});

						

						// Store reference to the watermark in the green box

						greenBox.watermark = img;

						
						// Store reference to the green box in the watermark

						img.greenBox = greenBox;

						

						// Add watermark to canvas

						canvas.add(img);

						// Position watermark above green box but below shapes

						canvas.sendToBack(img);

						// Move green box behind watermark

						canvas.sendToBack(greenBox);

						canvas.renderAll();

						

						console.log('Watermark loaded for box at:', centerX, centerY);

					}, function(err) {

						console.error('Failed to load watermark:', err);

					});

				}

				

				function loadWatermark() {

					// Clear existing watermarks first

					clearWatermarks();

					

					// Add watermarks to ALL green boxes (not just the initial box)

					const allGreenBoxes = canvas.getObjects().filter(obj => isGreenBox(obj));

					allGreenBoxes.forEach(box => {

						loadWatermarkForBox(box);

					});

					

					ensureWatermarksAtBack();

				}

				



				

				function clearWatermarks() {

					// Remove existing watermarks

					const objects = canvas.getObjects();

					for (let i = objects.length - 1; i >= 0; i--) {

						if (objects[i].isWatermark) {

							canvas.remove(objects[i]);

						}

					}

				}

				

				// Function to remove watermarks for specific green boxes

				function removeWatermarksForBoxes(boxesToRemove) {

					boxesToRemove.forEach(box => {

						if (box.watermark) {

							canvas.remove(box.watermark);

							box.watermark = null;

						}

					});

				}

				

				// Function to clean up orphaned watermarks

				function cleanupOrphanedWatermarks() {

					const objects = canvas.getObjects();

					for (let i = objects.length - 1; i >= 0; i--) {

						const obj = objects[i];

						if (obj.isWatermark && (!obj.greenBox || !canvas.contains(obj.greenBox))) {

							// Remove watermark if it doesn't have a green box or the green box is not on canvas

							canvas.remove(obj);

						}

					}

				}

				

				function updateWatermarks() {

					// Only reload watermark if initial box exists

					if (initialBox) {

						loadWatermark();

						ensureWatermarksAtBack();

					}

				}

				

				function ensureWatermarksAtBack() {

					// Proper layering: Green Boxes (bottom) -> Watermarks (middle) -> Shapes (top)

					const objects = canvas.getObjects();

					const watermarks = objects.filter(obj => obj.isWatermark);

					const greenBoxes = objects.filter(obj => isGreenBox(obj));

					const shapes = objects.filter(obj => !obj.isWatermark && !isGreenBox(obj));

					

					// First send all green boxes to the very back

					greenBoxes.forEach(box => {

						canvas.sendToBack(box);

					});

					

					// Then place watermarks above green boxes

					watermarks.forEach(watermark => {

						canvas.sendToBack(watermark);

						// Move above all green boxes

						greenBoxes.forEach(box => {

							canvas.sendToBack(box);

						});

					});

				}

				

				// Debug function to check canvas objects

				function debugCanvasObjects() {

					console.log('Canvas objects:');

					canvas.getObjects().forEach((obj, index) => {

						console.log(`${index}: Type: ${obj.type}, isWatermark: ${obj.isWatermark}, opacity: ${obj.opacity}, visible: ${obj.visible}`);

					});

				}

				

				// Watermark will be loaded when first green box is created

				// Debug canvas objects after a delay

				setTimeout(() => {

					debugCanvasObjects();

				}, 2000);

				

				// Custom scrollbar functionality

				let canvasContainer = document.getElementById('canvas-container');

				let canvasElement = document.getElementById('canvas');

				let horizontalScrollbar = document.getElementById('horizontal-scrollbar');

				let horizontalThumb = document.getElementById('horizontal-scrollbar-thumb');

				let verticalScrollbar = document.getElementById('vertical-scrollbar');

				let verticalThumb = document.getElementById('vertical-scrollbar-thumb');

				

				let scrollLeft = 0;

				let scrollTop = 0;

				let isDraggingHorizontal = false;

				let isDraggingVertical = false;

				

				function updateScrollbars() {

					const containerWidth = canvasContainer.clientWidth - 30; // Account for ruler

					const containerHeight = canvasContainer.clientHeight - 30; // Account for ruler

					

					// Calculate actual content bounds including all objects

					let contentBounds = { left: 0, top: 0, width: canvas.getWidth(), height: canvas.getHeight() };

					const objects = canvas.getObjects();

					

					if (objects.length > 0) {

						objects.forEach(obj => {

							const objBounds = obj.getBoundingRect();

							contentBounds.width = Math.max(contentBounds.width, objBounds.left + objBounds.width + 50);

							contentBounds.height = Math.max(contentBounds.height, objBounds.top + objBounds.height + 50);

						});

					}

					

					const canvasActualWidth = contentBounds.width;

					const canvasActualHeight = contentBounds.height;

					

					// Check if we're in fullscreen mode

					const isFullscreenActive = document.querySelector('.fullscreen-mode') !== null;

					

					// Horizontal scrollbar

					if (canvasActualWidth > containerWidth) {

						// Calculate available width for fixed positioned scrollbar

						const viewportWidth = window.innerWidth;

						let scrollbarWidth;

						

						if (isFullscreenActive) {

							scrollbarWidth = viewportWidth - 100; // Fullscreen: use most of viewport

						} else {

							scrollbarWidth = Math.min(containerWidth - 15, viewportWidth - 100); // Normal: limited width

						}

						

						horizontalScrollbar.style.width = scrollbarWidth + 'px';

						horizontalScrollbar.style.display = 'block';

						

						const scrollbarActualWidth = parseFloat(horizontalScrollbar.style.width);

						const thumbWidth = Math.max(30, (containerWidth / canvasActualWidth) * scrollbarActualWidth);

						horizontalThumb.style.width = thumbWidth + 'px';

						

						const maxScrollLeft = canvasActualWidth - containerWidth;

						const thumbLeft = (scrollLeft / maxScrollLeft) * (scrollbarActualWidth - thumbWidth);

						horizontalThumb.style.left = Math.max(2, Math.min(thumbLeft, scrollbarActualWidth - thumbWidth - 2)) + 'px';

					} else {

						horizontalScrollbar.style.display = 'none';

						scrollLeft = 0;

					}

					

					// Vertical scrollbar

					if (canvasActualHeight > containerHeight) {

						verticalScrollbar.style.height = (containerHeight - 15) + 'px'; // Account for horizontal scrollbar

						verticalScrollbar.style.display = 'block';

						

						const thumbHeight = Math.max(30, (containerHeight / canvasActualHeight) * containerHeight);

						verticalThumb.style.height = thumbHeight + 'px';

						

						const maxScrollTop = canvasActualHeight - containerHeight;

						const thumbTop = (scrollTop / maxScrollTop) * (containerHeight - thumbHeight);

						verticalThumb.style.top = Math.max(2, Math.min(thumbTop, containerHeight - thumbHeight - 2)) + 'px';

					} else {

						verticalScrollbar.style.display = 'none';

						scrollTop = 0;

					}

					

					// Update canvas position - ensure we can scroll to see all content

					const canvas_elem = document.getElementById('canvas');

					if (canvas_elem) {

						canvas_elem.style.marginLeft = (30 - scrollLeft) + 'px';

						canvas_elem.style.marginTop = (30 - scrollTop) + 'px';

					}

				}

				

				// Horizontal scrollbar drag functionality

				horizontalThumb.addEventListener('mousedown', function(e) {

					e.preventDefault();

					e.stopPropagation();

					

					isDraggingHorizontal = true;

					const startX = e.clientX;

					const thumbRect = horizontalThumb.getBoundingClientRect();

					const scrollbarRect = horizontalScrollbar.getBoundingClientRect();

					const startLeft = thumbRect.left - scrollbarRect.left;

					

					function onMouseMove(e) {

						if (!isDraggingHorizontal) return;

						e.preventDefault();

						

						const deltaX = e.clientX - startX;

						const scrollbarWidth = horizontalScrollbar.offsetWidth;

						const thumbWidth = horizontalThumb.offsetWidth;

						const maxThumbLeft = scrollbarWidth - thumbWidth - 4;

						

						let newThumbLeft = startLeft + deltaX;

						newThumbLeft = Math.max(2, Math.min(newThumbLeft, maxThumbLeft));

						

						horizontalThumb.style.left = newThumbLeft + 'px';

						

						// Calculate scroll position

						const scrollRatio = (newThumbLeft - 2) / (maxThumbLeft - 2);

						

						// Get actual content width

						const containerWidth = canvasContainer.clientWidth - 30;

						let contentWidth = canvas.getWidth();

						

						// Calculate content bounds

						const objects = canvas.getObjects();

						if (objects.length > 0) {

							objects.forEach(obj => {

								const objBounds = obj.getBoundingRect();

								contentWidth = Math.max(contentWidth, objBounds.left + objBounds.width + 50);

							});

						}

						

						const maxScrollLeft = Math.max(0, contentWidth - containerWidth);

						scrollLeft = scrollRatio * maxScrollLeft;

						

						// Update canvas position

						const canvas_elem = document.getElementById('canvas');

						if (canvas_elem) {

							canvas_elem.style.marginLeft = (30 - scrollLeft) + 'px';

						}

					}

					

					function onMouseUp(e) {

						e.preventDefault();

						isDraggingHorizontal = false;

						document.removeEventListener('mousemove', onMouseMove);

						document.removeEventListener('mouseup', onMouseUp);

					}

					

					document.addEventListener('mousemove', onMouseMove);

					document.addEventListener('mouseup', onMouseUp);

				});

				

				// Vertical scrollbar drag functionality

				verticalThumb.addEventListener('mousedown', function(e) {

					e.preventDefault();

					e.stopPropagation();

					

					isDraggingVertical = true;

					const startY = e.clientY;

					const thumbRect = verticalThumb.getBoundingClientRect();

					const scrollbarRect = verticalScrollbar.getBoundingClientRect();

					const startTop = thumbRect.top - scrollbarRect.top;

					

					function onMouseMove(e) {

						if (!isDraggingVertical) return;

						e.preventDefault();

						

						const deltaY = e.clientY - startY;

						const scrollbarHeight = verticalScrollbar.offsetHeight;

						const thumbHeight = verticalThumb.offsetHeight;

						const maxThumbTop = scrollbarHeight - thumbHeight - 4;

						

						let newThumbTop = startTop + deltaY;

						newThumbTop = Math.max(2, Math.min(newThumbTop, maxThumbTop));

						

						verticalThumb.style.top = newThumbTop + 'px';

						

						// Calculate scroll position

						const scrollRatio = (newThumbTop - 2) / (maxThumbTop - 2);

						

						// Get actual content height

						const containerHeight = canvasContainer.clientHeight - 30;

						let contentHeight = canvas.getHeight();

						

						// Calculate content bounds

						const objects = canvas.getObjects();

						if (objects.length > 0) {

							objects.forEach(obj => {

								const objBounds = obj.getBoundingRect();

								contentHeight = Math.max(contentHeight, objBounds.top + objBounds.height + 50);

							});

						}

						

						const maxScrollTop = Math.max(0, contentHeight - containerHeight);

						scrollTop = scrollRatio * maxScrollTop;

						

						// Update canvas position

						const canvas_elem = document.getElementById('canvas');

						if (canvas_elem) {

							canvas_elem.style.marginTop = (30 - scrollTop) + 'px';

						}

					}

					

					function onMouseUp(e) {

						e.preventDefault();

						isDraggingVertical = false;

						document.removeEventListener('mousemove', onMouseMove);

						document.removeEventListener('mouseup', onMouseUp);

					}

					

					document.addEventListener('mousemove', onMouseMove);

					document.addEventListener('mouseup', onMouseUp);

				});

				

				// Mouse wheel scrolling

				canvasContainer.addEventListener('wheel', function(e) {

					e.preventDefault();

					

					const containerWidth = canvasContainer.clientWidth - 30;

					const containerHeight = canvasContainer.clientHeight - 30;

					const canvasActualWidth = canvas.getWidth();

					const canvasActualHeight = canvas.getHeight();

					

					if (e.shiftKey) {

						// Horizontal scroll with Shift + wheel

						const maxScrollLeft = Math.max(0, canvasActualWidth - containerWidth);

						scrollLeft = Math.max(0, Math.min(scrollLeft + e.deltaY, maxScrollLeft));

					} else {

						// Vertical scroll

						const maxScrollTop = Math.max(0, canvasActualHeight - containerHeight);

						scrollTop = Math.max(0, Math.min(scrollTop + e.deltaY, maxScrollTop));

					}

					

					updateScrollbars();

				});

				

				// Update scrollbars when canvas size changes

				function updateScrollbarsOnResize() {

					updateScrollbars();

				}

				

				// Initial scrollbar setup

				updateScrollbars();

				

				// Click on scrollbar track to jump

				horizontalScrollbar.addEventListener('click', function(e) {

					if (e.target === horizontalScrollbar) {

						const rect = horizontalScrollbar.getBoundingClientRect();

						const clickX = e.clientX - rect.left;

						const scrollbarWidth = horizontalScrollbar.offsetWidth;

						const thumbWidth = horizontalThumb.offsetWidth;

						

						const newThumbLeft = Math.max(2, Math.min(clickX - thumbWidth/2, scrollbarWidth - thumbWidth - 2));

						horizontalThumb.style.left = newThumbLeft + 'px';

						

						const scrollRatio = (newThumbLeft - 2) / (scrollbarWidth - thumbWidth - 4);

						

						// Get actual content width

						const containerWidth = canvasContainer.clientWidth - 30;

						let contentWidth = canvas.getWidth();

						

						const objects = canvas.getObjects();

						if (objects.length > 0) {

							objects.forEach(obj => {

								const objBounds = obj.getBoundingRect();

								contentWidth = Math.max(contentWidth, objBounds.left + objBounds.width + 50);

							});

						}

						

						const maxScrollLeft = Math.max(0, contentWidth - containerWidth);

						scrollLeft = scrollRatio * maxScrollLeft;

						

						const canvas_elem = document.getElementById('canvas');

						if (canvas_elem) {

							canvas_elem.style.marginLeft = (30 - scrollLeft) + 'px';

						}

					}

				});

				

				// Update scrollbars when window resizes

				window.addEventListener('resize', updateScrollbarsOnResize);

				

				// Fix canvas offset on resize and fullscreen changes

				window.addEventListener('resize', function() {

					setTimeout(function() {

						// Update canvas width to match viewport

						const newWidth = window.innerWidth - 30;

						if (newWidth > canvas.getWidth()) {

							canvas.setWidth(newWidth);

							addRulers();

						}

						

						canvas.calcOffset();

						canvas.renderAll();

						updateScrollbars();

					}, 100);

				});

				



				// Add EdgeProfiles for Shapes in Modal

				var edgeProfiles = JSON.parse('<?=$_GET['edges']?>');

				var options = '';				

				Object.keys(edgeProfiles).forEach(function(key) {

					var title = edgeProfiles[key].title;

					var value = edgeProfiles[key].title.replace(/ /g, '-');

					options += '<option data-size="' + convertMmToPx(edgeProfiles[key].value) + '" value="' + value + '">' + title + '</option>';

				});



				jQuery('#shapeModal .form-group .edge-profile-section > div').each(function(){

					var length =  jQuery(this).data('count');

					var shape =  jQuery(this).find('img').data('shape');

					for ( var i=1; i <= length; i++ ) {

						jQuery(this).append('<select class="shapeEdgeProfile" name="' + shape + 'EdgeProfile' + i + '" id="shapeEdgeProfile' + i + '"><option value="0">Choose an option</option>' + options + '</select>');

					}

				});

				

				<?php if ( isset($_GET['video']) && !empty($_GET['video']) ) { ?>

				jQuery('#videoTutorialModal iframe').attr('src', decodeURIComponent('<?=$_GET['video']?>'));

				<?php } else { ?>

				jQuery('#videoTutorialModal iframe').after('<h3 style="color:red">No video has been found</h3>');

				jQuery('#videoTutorialModal iframe').remove();

				<?php } ?>

				

				

				function getObjectMM(shapeName, obj) {

					switch(shapeName) {

						case 'square':

						case 'rounded-square':

							if ( obj.length == undefined ) {

								totalMM += convertPxToMm(obj._objects[0].width * 2);

								totalMM += convertPxToMm(obj._objects[0].height * 2);

							} else {

								totalMM += convertPxToMm(obj[0]._objects[0].width * 2);

								totalMM += convertPxToMm(obj[0]._objects[0].height * 2);

								for (const [key, value] of Object.entries(obj)) {

									if ( key > 0 ) {

										totalMM += convertPxToMm(value.width * 2);

										totalMM += convertPxToMm(value.height * 2);

									}

								}

							}

							break;

						case 'circle':

							if ( obj.length < 4 ) {

								totalMM += convertPxToMm(obj[2]._objects[0].height * 3);

							} else {

								totalMM += convertPxToMm(obj[0].height);

							}

							break;

						case 'ellipse':

							if ( obj.length < 4 ) {

								totalMM += convertPxToMm(obj[2]._objects[0].width * 4);

								totalMM += convertPxToMm(obj[2]._objects[0].height * 4);

							} else {

								totalMM += convertPxToMm(obj[0].width * 2);

								totalMM += convertPxToMm(obj[0].height * 2);

							}

							break;

						case 'polygon':

							if ( obj.length == undefined ) {

								totalMM += convertPxToMm(obj._objects[0].width * 2);

								totalMM += convertPxToMm(obj._objects[0].height * 2);

							} else {

								totalMM += convertPxToMm(obj[0]._objects[0].width * 2);

								totalMM += convertPxToMm(obj[0]._objects[0].height * 2);

								for (const [key, value] of Object.entries(obj)) {

									if ( key > 0 ) {

										var edgeNo = value.customName.slice(-1);

										if ( edgeNo == '1' || edgeNo == '4' ) {

											totalMM += convertPxToMm(obj[0]._objects[0].width);

											totalMM += convertPxToMm(value.height * 2);

										} else {

											totalMM += convertPxToMm(value.width * 2);

											totalMM += convertPxToMm(value.height * 2);

										}

									}

								}

							}

							break;

						case 'triangle':

							if ( obj.length == undefined ) {

								totalMM += convertPxToMm(obj._objects[0].width);

								totalMM += parseInt(obj._objects[2].text.replace('mm', '')) * 2;

							} else {

								totalMM += convertPxToMm(obj[0]._objects[0].width);

								totalMM += parseInt(obj[0]._objects[2].text.replace('mm', '')) * 2;

								for (const [key, value] of Object.entries(obj)) {

									if ( key > 0 ) {

										totalMM += convertPxToMm(value.width * 2);

										totalMM += convertPxToMm(value.height * 2);

									}

								}

							}

							break;

							case 'U':

							if ( obj.length == undefined ) {

								totalMM += convertPxToMm(obj._objects[0].width * 2);

								totalMM += convertPxToMm(obj._objects[0].height * 2);

							} else {

								totalMM += convertPxToMm(obj[0]._objects[0].width * 2);

								totalMM += convertPxToMm(obj[0]._objects[0].height * 2);

								for (const [key, value] of Object.entries(obj)) {

									if ( key > 0 ) {

										totalMM += convertPxToMm(value.width * 2);

										totalMM += convertPxToMm(value.height * 2);

									}

								}

							}

							break;

						case 'shape-3':

							// Top edge

							var shape_3EdgeProfile1 = jQuery('select[name="shape-3EdgeProfile1"]').val();

							var topEdgeProfileHeight = jQuery('select[name="shape-3EdgeProfile1"] option:selected').data('size');

							if (shape_3EdgeProfile1 != '' && shape_3EdgeProfile1 != '0' && topEdgeProfileHeight != '0') {

								var topEdgeProfileText = jQuery('select[name="shape-3EdgeProfile1"] option:selected').text();

								shapesList.push(createEdgeProfileShape(

									{

										x: group.left,

										y: group.top - defaultGap - topEdgeProfileHeight,

										width: width,

										height: topEdgeProfileHeight,

										name: 'shape-3EdgeProfile1',

										text: topEdgeProfileText,

										value: shape_3EdgeProfile1,

									}

								));

							}

					}



				}

				

				// Function to get total length of objects

				function getTotalMM() {

					totalMM = 0;

					onlyCutAreaMM = 0;

					mitredEdgeAreaMM = 0;

					

					canvas.getObjects().forEach(obj => {

						if ( obj.mainShape ) {

							if ( obj._objects[0].type == 'group' ) {

								getObjectMM(obj._objects[0]._objects[0].shapeName, obj._objects);

							} else if ( obj._objects[0].type == 'circle' || obj._objects[0].type == 'ellipse' ) {

								getObjectMM(obj._objects[0].type, obj._objects);

							} else {

								getObjectMM(obj._objects[0].shapeName, obj);

							}

						} else if ( obj.customName && obj.customName.includes('EdgeProfile') ) {

							// This is a mitred edge profile

							mitredEdgeAreaMM += convertPxToMm(obj.width);

						}

					});

					

					// Calculate only cut area (total minus mitred edges)

					onlyCutAreaMM = totalMM;

					

					// Calculate grand total

					const grandTotal = onlyCutAreaMM + mitredEdgeAreaMM;

					

					// Update displays with proper formatting

					jQuery('.only_cut_mm').html(Math.round(onlyCutAreaMM));

					jQuery('.mitred_edge_mm').html(Math.round(mitredEdgeAreaMM));

					

					// Keep old total display for backward compatibility if it exists

					if (jQuery('.total_mms').length > 0) {

						jQuery('.total_mms').html(Math.round(grandTotal));

					}

					// Update slab usage calculation and visualization

					calculateSlabUsage();

					updateSlabVisualization();

				}

				

				

				// Function to check if shape is a green box

				function isGreenBox(object) {

					if ( object.hasOwnProperty('stroke') && object.stroke == "#41ba7e" ) {

						return true;

					} else {

						return false;

					}

				}

				

				

				// Helper function to check intersection between object bounds and box bounds

				function checkIntersection(bounds1, bounds2) {

					return !(bounds1.right < bounds2.left || 

							 bounds1.left > bounds2.right || 

							 bounds1.bottom < bounds2.top || 

							 bounds1.top > bounds2.bottom);

				}





				// Function to check if a shape's new position would overlap with other shapes

				function preventShapeOverlap(movingObject, newLeft, newTop) {

					if (!movingObject) return { left: newLeft, top: newTop };



					// Get moving object bounds with padding

					const movingBounds = getObjectBoundsWithPadding(movingObject, newLeft, newTop);

					let adjustedPosition = { left: newLeft, top: newTop };

					let hasCollision = false;



					// Check collision with other objects

					canvas.getObjects().forEach(obj => {

						if (obj === movingObject || isGreenBox(obj) || !obj.mainShape) return;



						const staticBounds = getObjectBoundsWithPadding(obj);

						if (checkIntersection(movingBounds, staticBounds)) {

							hasCollision = true;

							adjustedPosition = calculateNonOverlappingPosition(

								movingObject, 

								obj, 

								adjustedPosition.left, 

								adjustedPosition.top

							);

						}

					});



					// Check collision with green box

					if ( initialBox != null ) {



						const boxBounds = {

							left: initialBox.left + defaultGap,

							top: initialBox.top + defaultGap,

						};



						// Prevent shapes from going outside box boundaries (considering padding)

						if (movingBounds.left < boxBounds.left ||

							movingBounds.top < boxBounds.top) {



							adjustedPosition.left = Math.min(

								Math.max(adjustedPosition.left, boxBounds.left), (movingObject.width * movingObject.scaleX)

							);



							adjustedPosition.top = Math.min(

								Math.max(adjustedPosition.top, boxBounds.top), (movingObject.height * movingObject.scaleY)

							);

						}



					}





					return adjustedPosition;

				}



				// Helper function to get object bounds with padding

				function getObjectBoundsWithPadding(obj, customLeft, customTop) {

					// Use nullish coalescing to handle 0 values correctly

					const left = customLeft ?? obj.left;

					const top = customTop ?? obj.top;



					// Convert angle to radians, default to 0 if not present

					const angleInRadians = ((obj.angle || 0) * Math.PI) / 180;



					// Get dimensions with scale

					const width = obj.width * obj.scaleX;

					const height = obj.height * obj.scaleY;



					// Calculate trigonometric values once

					const cos = Math.cos(angleInRadians);

					const sin = Math.sin(angleInRadians);



					// Calculate rotatwidthed dimensions

					const rotatedWidth = Math.abs(width * cos) + Math.abs(height * sin);

					const rotatedHeight = Math.abs(width * sin) + Math.abs(height * cos);



					// Calculate half dimensions for easier center-based calculations

					const halfWidth = rotatedWidth / 2;

					const halfHeight = rotatedHeight / 2;



					// Return bounds with padding

					return {

						left: left - defaultGap,

						top: top - defaultGap,

						right: left + defaultGap,

						bottom: top + defaultGap,

						width:  width,

						height: height

					};

				}



				// Calculate non-overlapping position

				function calculateNonOverlappingPosition(movingObj, staticObj, newLeft, newTop) {

					const movingCenter = {

						x: newLeft + (movingObj.width * movingObj.scaleX) / 2,

						y: newTop + (movingObj.height * movingObj.scaleY) / 2

					};



					const staticCenter = {

						x: staticObj.left + (staticObj.width * staticObj.scaleX) / 2,

						y: staticObj.top + (staticObj.height * staticObj.scaleY) / 2

					};



					// Calculate direction vector

					const dx = movingCenter.x - staticCenter.x;

					const dy = movingCenter.y - staticCenter.y;



					// Calculate minimum required distance

					const minDistX = (movingObj.width * movingObj.scaleX + staticObj.width * staticObj.scaleX) / 2 + defaultGap;

					const minDistY = (movingObj.height * movingObj.scaleY + staticObj.height * staticObj.scaleY) / 2 + defaultGap;



					// Adjust position based on overlap

					let adjustedLeft = newLeft;

					let adjustedTop = newTop;



					if (Math.abs(dx) < minDistX) {

						adjustedLeft += dx < 0 ? -(minDistX - Math.abs(dx)) : (minDistX - Math.abs(dx));

					}



					if (Math.abs(dy) < minDistY) {

						adjustedTop += dy < 0 ? -(minDistY - Math.abs(dy)) : (minDistY - Math.abs(dy));

					}



					return { left: adjustedLeft, top: adjustedTop };

				}





				// Function to add a green-bordered box at a specified position

				function addBox(left, top) {

					// Check if box already exists at these coordinates

					const existingBox = infoBoxes.find(box => Math.abs(box.left - left) < 1 && Math.abs(box.top - top) < 1);



					if (existingBox) return; // Don't add duplicate boxes



					// Create new box

					const newBox = new fabric.Rect({

						left: left,

						top: top,

						width: boxWidth,

						height: boxHeight,

						fill: 'transparent',

						stroke: '#41ba7e',

						strokeWidth: 2,

						selectable: false,

						evented: false,

						hoverCursor: 'default'

					});



					// Add to canvas and array

					canvas.add(newBox);

					canvas.sendToBack(newBox);

					infoBoxes.push(newBox);

					

					// Add watermark to EVERY new green box

					loadWatermarkForBox(newBox);

					

					// Ensure watermarks stay behind everything

					ensureWatermarksAtBack();

					

					// Ensure watermarks stay behind everything

					ensureWatermarksAtBack();



					// If this is the first box, store it as initial box

					if (infoBoxes.length === 1) {

						initialBox = newBox;

					}



					// Update canvas size if needed

					const canvasWidth = Math.max(canvas.width, left + boxWidth + 50);

					const canvasHeight = Math.max(canvas.height, top + boxHeight + 50);

					if (canvasWidth > canvas.width || canvasHeight > canvas.height) {

						canvas.setDimensions({ width: canvasWidth, height: canvasHeight });

						addRulers();

						updateWatermarks(); // Update watermarks when canvas size changes

						setTimeout(() => updateScrollbars(), 100); // Update scrollbars when canvas size changes

					}

				}



				// Function to check if a box contains any objects

				function isBoxEmpty(box) {

					const boxBounds = {

						left: box.left,

						top: box.top,

						right: box.left + boxWidth,

						bottom: box.top + boxHeight

					};



					// Get all objects except infoBoxes

					const objects = canvas.getObjects().filter(obj => !infoBoxes.includes(obj) && obj.mainShape );



					// Check if any object intersects with this box

					return !objects.some(obj => {

						if (!obj.aCoords) return false;



						// Ensure object coordinates are up to date

						obj.setCoords();



						// Get the object's bounds after rotation

						const objCoords = [

							obj.aCoords.tl, // Top-left

							obj.aCoords.tr, // Top-right

							obj.aCoords.bl, // Bottom-left

							obj.aCoords.br  // Bottom-right

						];



						// Calculate the rotated object's bounding box

						const objBounds = {

							left: Math.min(objCoords[0].x, objCoords[1].x, objCoords[2].x, objCoords[3].x),

							top: Math.min(objCoords[0].y, objCoords[1].y, objCoords[2].y, objCoords[3].y),

							right: Math.max(objCoords[0].x, objCoords[1].x, objCoords[2].x, objCoords[3].x),

							bottom: Math.max(objCoords[0].y, objCoords[1].y, objCoords[2].y, objCoords[3].y)

						};



						// Check if the object's bounding box intersects with the current box

						return checkIntersection(objBounds, boxBounds);

					});

				}



				// Function to remove empty boxes

				function cleanupEmptyInfoBoxes() {

					// NEVER remove the initial/first box - it should always stay

					// Only remove NEW boxes that were created when dragging shapes

					

					// Get all boxes except the initial one (first box should never be removed)

					const boxesToCheck = infoBoxes.filter(box => box !== initialBox);



					boxesToCheck.forEach(box => {

						if (isBoxEmpty(box)) {

							// Remove associated watermark if it exists

							if (box.watermark) {

								canvas.remove(box.watermark);

								box.watermark = null;

							}

							

							canvas.remove(box);

							const index = infoBoxes.indexOf(box);

							if (index > -1) {

								infoBoxes.splice(index, 1);

							}

						}

					});

					

					// Clean up any orphaned watermarks

					cleanupOrphanedWatermarks();



				}





				// Function to add only one green box at a time (simplified version)

				function checkAndAddSingleBox(objectBounds, boxBounds) {

					// Only add one box at a time - check which direction the shape extends most

					const rightExtend = Math.max(0, objectBounds.right - boxBounds.right);

					const leftExtend = Math.max(0, boxBounds.left - objectBounds.left);

					const bottomExtend = Math.max(0, objectBounds.bottom - boxBounds.bottom);

					const topExtend = Math.max(0, boxBounds.top - objectBounds.top);

					

					// Find the direction with maximum extension

					const maxExtend = Math.max(rightExtend, leftExtend, bottomExtend, topExtend);

					

					// Only create one box in the direction of maximum extension

					if (maxExtend > 0) {

						if (maxExtend === rightExtend && rightExtend > 0) {

							// Add one box to the right

							addBox(boxBounds.right, boxBounds.top);

						} else if (maxExtend === leftExtend && leftExtend > 0) {

							// Add one box to the left

							addBox(boxBounds.left - boxWidth, boxBounds.top);

						} else if (maxExtend === topExtend && topExtend > 0) {

							// Add one box to the top

							addBox(boxBounds.left, boxBounds.top - boxHeight);

						} else if (maxExtend === bottomExtend && bottomExtend > 0) {

							// Add one box to the bottom

							addBox(boxBounds.left, boxBounds.bottom);

						}

					}

				}

				

				function checkAndAddBoxInDirection(objectBounds, boxBounds) {

					let boxesAdded = false;

					let iterations = 0;

					const MAX_ITERATIONS = 100; // Safety limit to prevent infinite loops



					// Keep checking until no new boxes are added or max iterations reached

					do {

						boxesAdded = false;

						iterations++;



						// Calculate how many boxes we need in each direction

						const rightBoxesNeeded = Math.ceil((objectBounds.right - boxBounds.right) / boxWidth);

						const leftBoxesNeeded = Math.ceil((boxBounds.left - objectBounds.left) / boxWidth);

						const bottomBoxesNeeded = Math.ceil((objectBounds.bottom - boxBounds.bottom) / boxHeight);

						const topBoxesNeeded = Math.ceil((boxBounds.top - objectBounds.top) / boxHeight);



						// Add boxes to the right

						for (let i = 0; i < rightBoxesNeeded; i++) {

							const x = boxBounds.right + (i * boxWidth);

							// Add boxes vertically along this column

							for (let j = Math.floor((objectBounds.top - boxBounds.top) / boxHeight); 

								 j <= Math.ceil((objectBounds.bottom - boxBounds.top) / boxHeight); 

								 j++) {

								addBox(x, boxBounds.top + (j * boxHeight));

								boxesAdded = true;

							}

						}



						// Add boxes to the left

						for (let i = 0; i < leftBoxesNeeded; i++) {

							const x = boxBounds.left - ((i + 1) * boxWidth);

							// Add boxes vertically along this column

							for (let j = Math.floor((objectBounds.top - boxBounds.top) / boxHeight); 

								 j <= Math.ceil((objectBounds.bottom - boxBounds.top) / boxHeight); 

								 j++) {

								addBox(x, boxBounds.top + (j * boxHeight));

								boxesAdded = true;

							}

						}



						// Add boxes to the bottom

						for (let i = 0; i < bottomBoxesNeeded; i++) {

							const y = boxBounds.bottom + (i * boxHeight);

							// Add boxes horizontally along this row

							for (let j = Math.floor((objectBounds.left - boxBounds.left) / boxWidth); 

								 j <= Math.ceil((objectBounds.right - boxBounds.left) / boxWidth); 

								 j++) {

								addBox(boxBounds.left + (j * boxWidth), y);

								boxesAdded = true;

							}

						}



						// Add boxes to the top

						for (let i = 0; i < topBoxesNeeded; i++) {

							const y = boxBounds.top - ((i + 1) * boxHeight);

							// Add boxes horizontally along this row

							for (let j = Math.floor((objectBounds.left - boxBounds.left) / boxWidth); 

								 j <= Math.ceil((objectBounds.right - boxBounds.left) / boxWidth); 

								 j++) {

								addBox(boxBounds.left + (j * boxWidth), y);

								boxesAdded = true;

							}

						}



						// Update boundaries after adding boxes

						if (boxesAdded) {

							boxBounds.right += rightBoxesNeeded * boxWidth;

							boxBounds.left -= leftBoxesNeeded * boxWidth;

							boxBounds.bottom += bottomBoxesNeeded * boxHeight;

							boxBounds.top -= topBoxesNeeded * boxHeight;

						}



					} while (boxesAdded && iterations < MAX_ITERATIONS);



					return boxesAdded;

				}





				// Function to check if the shape exceeds any box boundaries

				function checkShapeBounds(object) {

					if (!object || !object.aCoords) return;



					// Ensure the object's coordinates are updated

					object.setCoords();



					// Get all coordinates after rotation

					const objectCoords = [

						object.aCoords.tl, // Top-left

						object.aCoords.tr, // Top-right

						object.aCoords.bl, // Bottom-left

						object.aCoords.br  // Bottom-right

					];



					// Calculate the rotated object's bounding box

					const objectBounds = {

						left: Math.min(objectCoords[0].x, objectCoords[1].x, objectCoords[2].x, objectCoords[3].x),

						top: Math.min(objectCoords[0].y, objectCoords[1].y, objectCoords[2].y, objectCoords[3].y),

						right: Math.max(objectCoords[0].x, objectCoords[1].x, objectCoords[2].x, objectCoords[3].x),

						bottom: Math.max(objectCoords[0].y, objectCoords[1].y, objectCoords[2].y, objectCoords[3].y)

					};



					// Now check against the existing boxes for overlap

					infoBoxes.forEach(box => {

						const boxBounds = {

							left: box.left,

							top: box.top,

							right: box.left + boxWidth,

							bottom: box.top + boxHeight

						};



						// If object overlaps the box, add new boxes if necessary (only one at a time)

						if (checkIntersection(objectBounds, boxBounds)) {

							checkAndAddSingleBox(objectBounds, boxBounds);

						}

					});



					// Cleanup any empty boxes

					cleanupEmptyInfoBoxes();

				}





				// Set up event listener to detect shape movement and check for overlap with the 20mm gap

				canvas.on('object:modified', function(e) {

					cleanupEmptyInfoBoxes();
					
					// Clean up orphaned watermarks when objects are modified
					cleanupOrphanedWatermarks();
					
					// Update slab usage calculation and visualization when objects are moved
					calculateSlabUsage();
					updateSlabVisualization();

				});





				// Add initial detection box to the canvas

				addBox(0, 0); // Center the box

				

				// Force a render to ensure everything is displayed

				canvas.renderAll();
				
				// Initialize slab usage calculation and visualization
				setTimeout(() => {
					calculateSlabUsage();
					updateSlabVisualization();
				}, 500);



				

				// Function to draw the ruler

				function addRulers() {

					jQuery('#ruler-x, #ruler-y').empty(); // Clear previous rulers



					// Calculate optimal step size based on zoom level

					let step = rulerInterval;

					while (step * zoomLevel < 40) step *= 2;

					while (step * zoomLevel > 100) step /= 2;



					const visibleWidth = canvas.width / zoomLevel;  // Calculate visible area width

					const visibleHeight = canvas.height / zoomLevel; // Calculate visible area height



					// Ruler on the X-axis

					for (let i = 0; i <= visibleWidth; i += step) {

						const pixelPosition = i * zoomLevel + 30; // Adding 30px for ruler container offset

						jQuery('<div class="ruler-text"></div>')

							.text(convertPxToMm(i))

							.css({

							left: pixelPosition + 'px',

							top: '7px',

						}).appendTo('#ruler-x');

					}



					// Ruler on the Y-axis

					for (let i = 0; i <= visibleHeight; i += step) {

						const pixelPosition = i * zoomLevel + 30; // Adding 30px for ruler container offset

						jQuery('<div class="ruler-text"></div>')

							.text(convertPxToMm(i))

							.css({

							top: pixelPosition + 'px',

							right: '10px',

						}).appendTo('#ruler-y');

					}



					// Adjust ruler container sizes based on zoom level and canvas size

					jQuery('#ruler-x').width(visibleWidth * zoomLevel);

					jQuery('#ruler-y').height(visibleHeight * zoomLevel);

				}





				// Zoom function with ruler update

				function zoomCanvas(zoomIn) {

					if (zoomIn) {

						zoomLevel *= 1.1; // Zoom in

					} else {

						zoomLevel /= 1.1; // Zoom out

					}



					zoomLevel = Math.round(zoomLevel * 100) / 100; // Round to two decimal places for precision



					canvas.setZoom(zoomLevel); // Apply zoom to canvas

					addRulers(); // Re-render rulers to adjust for zoom

				}



				// Add initial rulers

				

				canvas.setZoom(zoomLevel);

				

				addRulers();



				// Reset Form Fields

				function resetShapeModal() {

					jQuery('#shapeModal .form-group #shapeName').val('');

					jQuery('#shapeModal .form-group #shapeWidth').parent().show();

					jQuery('#shapeModal .form-group #shapeWidth').val(defaultShapeWidth);

					jQuery('#shapeModal .form-group #shapeHeight').val(defaultShapeHeight);

					jQuery('#shapeModal .form-group .edge-profile-section > div').hide()

					jQuery('#shapeModal .form-group .edge-profile-section select').val('0');

					jQuery('#saveShapeDetails').attr('data-state', 'save');

				}

				

				

				function calculateFontSize(width, height) {

					// Define a base font size and scaling factor

					const baseFontSize = 1; // Base font size in pixels

					const scaleFactor = 0.2;  // Scale factor for adjusting the font size



					// Calculate the suitable font size based on width and height

					// This example assumes you want to use the smaller dimension to determine font size

					const smallerDimension = Math.min(width, height);



					// Calculate font size as a percentage of the smaller dimension

					const fontSize = Math.floor(smallerDimension * scaleFactor);



					// Set a minimum font size to avoid very small text

					return Math.max(fontSize, baseFontSize);

				}





				// Function to create blank shapes at each corner

				function createEdgeProfileShape(shapeObject) {

					var shape;

					switch(shapeObject.type) {

						case 'circle':

							shape = new fabric.Circle({

								radius: shapeObject.height / 2,

							});

							break;

						case 'ellipse':

							shape = new fabric.Ellipse({

								rx: shapeObject.width / 2,

								ry: shapeObject.height / 2

							});

							break;

						default:

							shape = new fabric.Rect({

								width: shapeObject.width,

								height: shapeObject.height

							});

							break;

					}

					

					shape.set({

						left: shapeObject.x,

						top: shapeObject.y,

						fill: 'transparent',

						stroke: 'black',

						customName: shapeObject.name,

						customValue: shapeObject.value,

						customText: shapeObject.text,

					});



					if ( shapeObject.hasOwnProperty('angle') ) {

						shape.angle = shapeObject.angle;

					}



					// Add the group to the canvas

					canvas.add(shape);

					shape.setCoords();

					canvas.requestRenderAll();

					

					return shape;

				}



				

				// Function to Shapes

				function createShape(shapeObject) {

					var shape;

					switch(shapeObject.type) {

						case 'square':

							shape = new fabric.Rect({

								width: shapeObject.width,

								height: shapeObject.height,

							});

							break;

						case 'rounded-square':

							shape = new fabric.Rect({

								width: shapeObject.width,

								height: shapeObject.height,

								rx: 10,

								ry: 10

							});

							break;

						case 'circle':

							shape = new fabric.Circle({

								radius: shapeObject.height / 2,

							});

							break;

						case 'ellipse':

							shape = new fabric.Ellipse({

								rx: shapeObject.width / 2,

								ry: shapeObject.height / 2

							});

							break;

						case 'polygon':							

							const points = [

								{ x: shapeObject.width/4, y: 0 },           // Top left

								{ x: shapeObject.width * 3/4, y: 0 },       // Top right

								{ x: shapeObject.width, y: shapeObject.height/2 },      // Middle right

								{ x: shapeObject.width * 3/4, y: shapeObject.height },  // Bottom right

								{ x: shapeObject.width/4, y: shapeObject.height },      // Bottom left

								{ x: 0, y: shapeObject.height/2 }           // Middle left

							];

							



							shape = new fabric.Polygon(points);

							break;

						case 'triangle':

							shape = new fabric.Triangle({

								width: shapeObject.width,

								height: shapeObject.height,

							});

							break;

						case 'L': {

							// L shape drawn as a closed polyline (outline only)

							const thickness = Math.min(shapeObject.width, shapeObject.height) / 3;

							const pts = [

								{ x: 0, y: 0 },

								{ x: shapeObject.width, y: 0 },

								{ x: shapeObject.width, y: thickness },

								{ x: thickness, y: thickness },

								{ x: thickness, y: shapeObject.height },

								{ x: 0, y: shapeObject.height },

								{ x: 0, y: 0 }

							];

							shape = new fabric.Polyline(pts, { closed: true });

							break;

						}

                        case 'U': {

                            // // Pixel-perfect U recess: thinner side walls and deeper inner legs

                            // const bar = Math.max(4, Math.round(Math.min(shapeObject.width, shapeObject.height) * 0.10)); // thinner outer wall

                            // const gapRatio = 0.26;   // notch width relative to total width

                            // const depthRatio = 0.70; // increase inner leg height (deeper notch)



                            // const gapWidth = Math.round(shapeObject.width * gapRatio);

                            // const notchDepth = Math.round(shapeObject.height * depthRatio);

                            // const gapLeft = Math.round((shapeObject.width - gapWidth) / 2);

                            // const gapRight = gapLeft + gapWidth;



                            // // Outer frame: top, left, right, bottom-left segment, bottom-right segment

                            // const topBar = new fabric.Rect({ left: 0, top: 0, width: shapeObject.width, height: bar, fill: 'black', strokeWidth: 0 });

                            // const leftOuter = new fabric.Rect({ left: 0, top: 0, width: bar, height: shapeObject.height, fill: 'black', strokeWidth: 0 });

                            // const rightOuter = new fabric.Rect({ left: shapeObject.width - bar, top: 0, width: bar, height: shapeObject.height, fill: 'black', strokeWidth: 0 });

                            // const bottomLeft = new fabric.Rect({ left: 0, top: shapeObject.height - bar, width: gapLeft, height: bar, fill: 'black', strokeWidth: 0 });

                            // const bottomRight = new fabric.Rect({ left: gapRight, top: shapeObject.height - bar, width: shapeObject.width - gapRight, height: bar, fill: 'black', strokeWidth: 0 });



                            // // Inner notch walls (slightly thinner than outer walls for definition)

                            // const innerBar = Math.max(3, Math.round(bar * 0.9));

                            // const notchLeft = new fabric.Rect({ left: gapLeft, top: shapeObject.height - notchDepth, width: innerBar, height: notchDepth, fill: 'black', strokeWidth: 0 });

                            // const notchRight = new fabric.Rect({ left: gapRight - innerBar, top: shapeObject.height - notchDepth, width: innerBar, height: notchDepth, fill: 'black', strokeWidth: 0 });

                            // const notchTop = new fabric.Rect({ left: gapLeft, top: shapeObject.height - notchDepth, width: gapWidth, height: innerBar, fill: 'black', strokeWidth: 0 });



                            // shape = new fabric.Group([topBar, leftOuter, rightOuter, bottomLeft, bottomRight, notchLeft, notchRight, notchTop]);

                            // break;

							const uPoints = [

								{ x: 0, y: 0 },                    // Top left

								{ x: shapeObject.width, y: 0 },     // Top right

								{ x: shapeObject.width, y: shapeObject.height }, // Bottom right

								{ x: shapeObject.width * 0.8, y: shapeObject.height }, // Inner right

								{ x: shapeObject.width * 0.8, y: shapeObject.height * 0.3 }, // Inner top right

								{ x: shapeObject.width * 0.2, y: shapeObject.height * 0.3 }, // Inner top left

								{ x: shapeObject.width * 0.2, y: shapeObject.height }, // Inner left

								{ x: 0, y: shapeObject.height }  // Bottom left

							];

							shape = new fabric.Polygon(uPoints, {

								strokeLineJoin: 'miter',

								strokeLineCap: 'butt'

							});

							break;

                        }

                        case 'shape-3': {

                            // Trapezoid (smaller top edge, wider base)

                            const topRatio = 0.6; // top width relative to base

                            const topWidth = shapeObject.width * topRatio;

                            const leftOffset = (shapeObject.width - topWidth) / 2;

                            const pts = [

                                { x: leftOffset, y: 0 },                        // top-left

                                { x: leftOffset + topWidth, y: 0 },              // top-right

                                { x: shapeObject.width, y: shapeObject.height }, // bottom-right

                                { x: 0, y: shapeObject.height }                  // bottom-left

                            ];

                            shape = new fabric.Polygon(pts);

                            break;

                        }

					}



                    // Update shape properties

                    shape.fill = 'transparent';

                    shape.stroke = 'black';

                    shape.strokeWidth = 6; // match icon thickness

                    shape.strokeLineJoin = 'miter';

                    shape.strokeLineCap = 'butt';

                    shape.strokeUniform = true;

                    shape.strokeMiterLimit = 10;

                    shape.originX = 'center';

                    shape.originY = 'center';

					shape.shapeName = shapeObject.type;					



					// Create a fabric.Text object for the name

					var text = new fabric.Text(shapeObject.text, {

						fontSize: calculateFontSize(shapeObject.width, shapeObject.height) / 2,

						originX: 'center',

						originY: 'center',

						fill: 'black',

					});

	

					

					if ( shapeObject.type == 'triangle' ) {

						text.set({

							fontSize: text.fontSize / 2

						});

					}

					

					var groupArr = [shape, text];



					const fontSize = calculateFontSize(shapeObject.width, shapeObject.height) / 3;



					if ( shapeObject.type == 'square' || shapeObject.type == 'rounded-square' ) {

						// Top corner

						var topText = new fabric.Text(String(convertPxToMm(shapeObject.width)) + "mm", {

							fontSize: fontSize,

							originX: 'center',

							fill: 'black',

							top: - ( shape.height / 2 ) + defaultGap

						});



						var squareEdgeProfile1 = jQuery('select[name="' + shapeObject.type + 'EdgeProfile1"]').val();

						if (squareEdgeProfile1 != '' && squareEdgeProfile1 != '0') {

							var topEdgeProfileText = jQuery('select[name="' + shapeObject.type + 'EdgeProfile1"] option:selected').text();

						} else {

							var topEdgeProfileText = 'Unfinished';

						}



						var topEdgeProfile = new fabric.Text(topEdgeProfileText, {

							fontSize: topText.fontSize,

							originX: topText.originX,

							fill: topText.fill,

							top: topText.top + fontSize

						});





						// Right Corner

						var rightText = new fabric.Text(String(convertPxToMm(shapeObject.height)) + "mm", {

							fontSize: fontSize,

							fill: 'black',

							angle: 90,

							top: - fontSize - defaultGap,

							left: ( shape.width / 2 ) - defaultGap,

						});



						var squareEdgeProfile2 = jQuery('select[name="' + shapeObject.type + 'EdgeProfile2"]').val();

						if (squareEdgeProfile2 != '' && squareEdgeProfile2 != '0') {

							var rightEdgeProfileText = jQuery('select[name="' + shapeObject.type + 'EdgeProfile2"] option:selected').text();

						} else {

							var rightEdgeProfileText = 'Unfinished';

						}



						var rightEdgeProfile = new fabric.Text(rightEdgeProfileText, {

							fontSize: rightText.fontSize,

							fill: rightText.fill,

							angle: rightText.angle,

							left: rightText.left - fontSize,

							top: rightText.top

						});





						// Bottom Corner

						var bottomText = new fabric.Text(String(convertPxToMm(shapeObject.width)) + "mm", {

							fontSize: fontSize,

							originX: 'center',

							fill: 'black',

							top: ( shape.height / 2 ) - fontSize - defaultGap

						});



						var squareEdgeProfile3 = jQuery('select[name="' + shapeObject.type + 'EdgeProfile3"]').val();

						if (squareEdgeProfile3 != '' && squareEdgeProfile3 != '0') {

							var bottomEdgeProfileText = jQuery('select[name="' + shapeObject.type + 'EdgeProfile3"] option:selected').text();

						} else {

							var bottomEdgeProfileText = 'Unfinished';

						}



						var bottomEdgeProfile = new fabric.Text(bottomEdgeProfileText, {

							fontSize: bottomText.fontSize,

							originX: bottomText.originX,

							fill: bottomText.fill,

							top: bottomText.top - fontSize

						});





						// Left Corner

						var leftText = new fabric.Text(String(convertPxToMm(shapeObject.height)) + "mm", {

							fontSize: fontSize,

							fill: 'black',

							angle: -90,

							top: fontSize + defaultGap,

							left: - ( shape.width / 2 ) + defaultGap,

						});



						var squareEdgeProfile4 = jQuery('select[name="' + shapeObject.type + 'EdgeProfile4"]').val();

						if (squareEdgeProfile4 != '' && squareEdgeProfile4 != '0') {

							var leftEdgeProfileText = jQuery('select[name="' + shapeObject.type + 'EdgeProfile4"] option:selected').text();

						} else {

							var leftEdgeProfileText = 'Unfinished';

						}



						var leftEdgeProfile = new fabric.Text(leftEdgeProfileText, {

							fontSize: leftText.fontSize,

							fill: leftText.fill,

							angle: leftText.angle,

							left: leftText.left + fontSize,

							top: leftText.top

						});





						groupArr.push(topText);

						groupArr.push(topEdgeProfile);

						groupArr.push(rightText);

						groupArr.push(rightEdgeProfile);

						groupArr.push(bottomText);

						groupArr.push(bottomEdgeProfile);

						groupArr.push(leftText);

						groupArr.push(leftEdgeProfile);

					} else if ( shapeObject.type == 'circle' ) {

						var heightText = new fabric.Text(String(convertPxToMm(shapeObject.height)) + "mm", {

							fontSize: fontSize,

							originX: 'center',

							fill: 'black',

							top: fontSize + defaultGap

						});



						var circleEdgeProfile1 = jQuery('select[name="circleEdgeProfile1"]').val();

						if (circleEdgeProfile1 != '' && circleEdgeProfile1 != '0') {

							var circleEdgeProfileText = jQuery('select[name="circleEdgeProfile1"] option:selected').text();

						} else {

							var circleEdgeProfileText = 'Unfinished';

						}



						var circleEdgeProfileText = new fabric.Text(circleEdgeProfileText, {

							fontSize: heightText.fontSize,

							originX: heightText.originX,

							fill: heightText.fill,

							top: heightText.top + fontSize

						});



						groupArr.push(heightText);

						groupArr.push(circleEdgeProfileText);

					} else if ( shapeObject.type == 'polygon' ) {

						let sideLengths = [];

						let angles = [];



						// For polygons

						if (shape.type === 'polygon') {

							let points = shape.points;

							for (let i = 0; i < points.length; i++) {

								// Get current point and next point

								const currentPoint = points[i];

								const nextPoint = points[(i + 1) % points.length];



								// Calculate side length

								const length = Math.sqrt(

									Math.pow(nextPoint.x - currentPoint.x, 2) + 

									Math.pow(nextPoint.y - currentPoint.y, 2)

								);

								sideLengths.push(Math.ceil(convertPxToMm(length)));



								// Calculate angle

								const deltaX = nextPoint.x - currentPoint.x;

								const deltaY = nextPoint.y - currentPoint.y;

								// Calculate absolute angle in degrees

								let angle = Math.atan2(deltaY, deltaX) * (180 / Math.PI);



								// Normalize angle to be between 0 and 360

								if (angle < 0) {

									angle += 360;

								}

								angles.push(angle);

							}

						}





						// Top of Object

						var topText = new fabric.Text(String(sideLengths[0]) + "mm", {

							fontSize: fontSize,

							originX: 'center',

							fill: 'black',

							top: - ( shape.height / 2 ) + defaultGap

						});



						var polygonEdgeProfile1 = jQuery('select[name="polygonEdgeProfile1"]').val();

						if (polygonEdgeProfile1 != '' && polygonEdgeProfile1 != '0') {

							var topEdgeProfileText = jQuery('select[name="polygonEdgeProfile1"] option:selected').text();

						} else {

							var topEdgeProfileText = 'Unfinished';

						}



						var topEdgeProfile = new fabric.Text(topEdgeProfileText, {

							fontSize: topText.fontSize,

							originX: topText.originX,

							fill: topText.fill,

							top: topText.top + fontSize

						});





						// Top Right of Object

						var topRightText = new fabric.Text(String(sideLengths[1]) + "mm", {

							fontSize: fontSize,

							fill: 'black',

							angle: 65,

							top:  - ( shape.height / 3 ),

							left: ( shape.width / 3 ) - defaultGap,

							customAngle: angles[1] - 90

						});



						var polygonEdgeProfile2 = jQuery('select[name="polygonEdgeProfile2"]').val();

						if (polygonEdgeProfile2 != '' && polygonEdgeProfile2 != '0') {

							var topRightEdgeProfileText = jQuery('select[name="polygonEdgeProfile2"] option:selected').text();

						} else {

							var topRightEdgeProfileText = 'Unfinished';

						}



						var topRightEdgeProfile = new fabric.Text(topRightEdgeProfileText, {

							fontSize: topRightText.fontSize,

							fill: topRightText.fill,

							angle: topRightText.angle,

							left: topRightText.left - fontSize,

							top: topRightText.top + defaultGap

						});

						



						// Bottom Right of Object

						var bottomRightText = new fabric.Text(String(sideLengths[2]) + "mm", {

							fontSize: fontSize,

							fill: 'black',

							angle: 115,

							top:  ( shape.height / 4 ) - defaultGap - fontSize,

							left: ( shape.width / 3 ) + defaultGap + fontSize,

							customAngle: angles[2] - 90

						});



						var polygonEdgeProfile3 = jQuery('select[name="polygonEdgeProfile3"]').val();

						if (polygonEdgeProfile3 != '' && polygonEdgeProfile3 != '0') {

							var bottomRightEdgeProfileText = jQuery('select[name="polygonEdgeProfile3"] option:selected').text();

						} else {

							var bottomRightEdgeProfileText = 'Unfinished';

						}



						var bottomRightEdgeProfile = new fabric.Text(bottomRightEdgeProfileText, {

							fontSize: bottomRightText.fontSize,

							fill: bottomRightText.fill,

							angle: bottomRightText.angle,

							left: bottomRightText.left - fontSize,

							top: bottomRightText.top - fontSize

						});





						// Bottom of Object

						var bottomText = new fabric.Text(String(sideLengths[3]) + "mm", {

							fontSize: fontSize,

							originX: 'center',

							fill: 'black',

							top: ( shape.height / 2 ) - defaultGap - fontSize

						});



						var polygonEdgeProfile4 = jQuery('select[name="polygonEdgeProfile4"]').val();

						if (polygonEdgeProfile4 != '' && polygonEdgeProfile4 != '0') {

							var bottomEdgeProfileText = jQuery('select[name="polygonEdgeProfile4"] option:selected').text();

						} else {

							var bottomEdgeProfileText = 'Unfinished';

						}



						var bottomEdgeProfile = new fabric.Text(bottomEdgeProfileText, {

							fontSize: bottomText.fontSize,

							originX: bottomText.originX,

							fill: bottomText.fill,

							top: bottomText.top - fontSize

						});





						// Bottom Right of Object

						var bottomLeftText = new fabric.Text(String(sideLengths[4]) + "mm", {

							fontSize: fontSize,

							fill: 'black',

							top:  ( shape.height / 3 ) - defaultGap,

							left: - ( shape.width / 3 ),

							angle: -120,

							customAngle: angles[1] - 90

						});



						var polygonEdgeProfile5 = jQuery('select[name="polygonEdgeProfile5"]').val();

						if (polygonEdgeProfile5 != '' && polygonEdgeProfile5 != '0') {

							var bottomLeftEdgeProfileText = jQuery('select[name="polygonEdgeProfile5"] option:selected').text();

						} else {

							var bottomLeftEdgeProfileText = 'Unfinished';

						}



						var bottomLeftEdgeProfile = new fabric.Text(bottomLeftEdgeProfileText, {

							fontSize: bottomLeftText.fontSize,

							fill: bottomLeftText.fill,

							angle: bottomLeftText.angle,

							left: bottomLeftText.left + fontSize,

							top: bottomLeftText.top

						});

						

						

						// Top Left of Object

						var topLeftText = new fabric.Text(String(sideLengths[5]) + "mm", {

							fontSize: fontSize,

							fill: 'black',

							angle: -65,

							top:  - ( shape.height / 4 ) + fontSize + defaultGap,

							left: - ( shape.width / 3 ) - defaultGap - fontSize,

							customAngle: angles[2] - 90

						});



						var polygonEdgeProfile6 = jQuery('select[name="polygonEdgeProfile6"]').val();

						if (polygonEdgeProfile6 != '' && polygonEdgeProfile6 != '0') {

							var topLeftEdgeProfileText = jQuery('select[name="polygonEdgeProfile6"] option:selected').text();

						} else {

							var topLeftEdgeProfileText = 'Unfinished';

						}



						var topLeftEdgeProfile = new fabric.Text(topLeftEdgeProfileText, {

							fontSize: topLeftText.fontSize,

							fill: topLeftText.fill,

							angle: topLeftText.angle,

							left: topLeftText.left + fontSize,

							top: topLeftText.top + fontSize

						});





						groupArr.push(topText);

						groupArr.push(topEdgeProfile);

						groupArr.push(topRightText);

						groupArr.push(topRightEdgeProfile);

						groupArr.push(bottomRightText);

						groupArr.push(bottomRightEdgeProfile);

						groupArr.push(bottomText);

						groupArr.push(bottomEdgeProfile);

						groupArr.push(bottomLeftText);

						groupArr.push(bottomLeftEdgeProfile);

						groupArr.push(topLeftText);

						groupArr.push(topLeftEdgeProfile);

					} else if ( shapeObject.type == 'triangle' ) {

						let sideLength = Math.ceil(convertPxToMm(Math.sqrt(Math.pow(shape.width/2, 2) + Math.pow(shape.height, 2))));



						// Get the side lengths of the triangle

						let a = Math.ceil(convertPxToMm(shape.width / 2));

						let b = Math.ceil(convertPxToMm(Math.sqrt(Math.pow(shape.width / 2, 2) + Math.pow(shape.height, 2))));

						let c = Math.ceil(convertPxToMm(shape.height));



						// Use the Law of Cosines to calculate the bend angle

						let angle = Math.acos((Math.pow(b, 2) + Math.pow(c, 2) - Math.pow(a, 2)) / (2 * b * c)) * (180 / Math.PI);

						

						

						// Right of Object

						var rightText = new fabric.Text(String(sideLength) + "mm", {

							fontSize: fontSize,

							originX: 'left',

							originY: 'top',

							fill: 'black',

							angle: 65,

							left: shape.left + ( shape.width / 4 ),

							top: shape.top + fontSize + ( defaultGap * 4 ),

							customAngle: -angle

						});



						var triangleEdgeProfile1 = jQuery('select[name="triangleEdgeProfile1"]').val();

						if (triangleEdgeProfile1 != '' && triangleEdgeProfile1 != '0') {

							var topRightEdgeProfileText = jQuery('select[name="triangleEdgeProfile1"] option:selected').text();

						} else {

							var topRightEdgeProfileText = 'Unfinished';

						}



						var topRightEdgeProfile = new fabric.Text(topRightEdgeProfileText, {

							fontSize: rightText.fontSize,

							originX: rightText.originX,

							originY: rightText.originY,

							fill: rightText.fill,

							angle: rightText.angle,

							left: rightText.left - fontSize,

							top: rightText.top

						});





						// Width of Object

						var widthText = new fabric.Text(String(convertPxToMm(shapeObject.width)) + "mm", {

							fontSize: fontSize,

							originX: 'center',

							originY: 'top',

							fill: 'black',

							top: shape.top + ( shape.height / 2 ) - fontSize - ( defaultGap * 2 )

						});



						var triangleEdgeProfile2 = jQuery('select[name="triangleEdgeProfile2"]').val();

						if (triangleEdgeProfile2 != '' && triangleEdgeProfile2 != '0') {

							var bottomEdgeProfileText = jQuery('select[name="triangleEdgeProfile2"] option:selected').text();

						} else {

							var bottomEdgeProfileText = 'Unfinished';

						}



						var bottomEdgeProfile = new fabric.Text(bottomEdgeProfileText, {

							fontSize: widthText.fontSize,

							originX: widthText.originX,

							originY: widthText.originY,

							fill: widthText.fill,

							top: widthText.top - fontSize

						});





						// Left of Object

						var leftText = new fabric.Text(String(sideLength) + "mm", {

							fontSize: fontSize,

							originX: 'left',

							originY: 'top',

							fill: 'black',

							angle: -65,

							left: shape.left - ( shape.width / 3 ),

							top: shape.top + ( shape.height / 4 ),

							customAngle: angle

						});



						var triangleEdgeProfile3 = jQuery('select[name="triangleEdgeProfile3"]').val();

						if (triangleEdgeProfile3 != '' && triangleEdgeProfile3 != '0') {

							var topLeftEdgeProfileValue = jQuery('select[name="triangleEdgeProfile3"] option:selected').text();

						} else {

							var topLeftEdgeProfileValue = 'Unfinished';

						}



						var topLeftEdgeProfile = new fabric.Text(topLeftEdgeProfileValue, {

							fontSize: leftText.fontSize,

							originX: leftText.originX,

							originY: leftText.originY,

							fill: leftText.fill,

							angle: leftText.angle,

							left: leftText.left + fontSize,

							top: leftText.top + ( fontSize / 2)

						});





						groupArr.push(rightText);

						groupArr.push(topRightEdgeProfile);

						groupArr.push(widthText);

						groupArr.push(bottomEdgeProfile);

						groupArr.push(leftText);

						groupArr.push(topLeftEdgeProfile);

					} else if ( shapeObject.type == 'ellipse' ) {

						// Height of Object

						var heightText = new fabric.Text(String(convertPxToMm(shapeObject.height)) + "mm", {

							fontSize: fontSize,

							originX: 'right',

							originY: 'top',

							fill: 'black',

							angle: 90,

							left: shape.left + ( shape.width / 2 ) - fontSize - ( defaultGap * 2 ),

							top: fontSize + ( defaultGap * 2 )

						});



						// Width of Object

						var widthText = new fabric.Text(String(convertPxToMm(shapeObject.width)) + "mm", {

							fontSize: fontSize,

							originX: 'center',

							originY: 'top',

							fill: 'black',

							top: shape.top + ( shape.height / 2 ) - fontSize - ( defaultGap * 2 )

						});

						

						var ellipseEdgeProfile1 = jQuery('select[name="ellipseEdgeProfile1"]').val();

						if (ellipseEdgeProfile1 != '' && ellipseEdgeProfile1 != '0') {

							var ellipseEdgeProfileText = jQuery('select[name="ellipseEdgeProfile1"] option:selected').text();

						} else {

							var ellipseEdgeProfileText = 'Unfinished';

						}



						var ellipseEdgeProfile = new fabric.Text(ellipseEdgeProfileText, {

							fontSize: widthText.fontSize,

							originX: widthText.originX,

							originY: widthText.originY,

							fill: widthText.fill,

							top: widthText.top - 10

						});

						

						groupArr.push(widthText);

						groupArr.push(heightText);

						groupArr.push(ellipseEdgeProfile);

					}else if ( shapeObject.type == 'U' ) {

						// Top edge

						var topText = new fabric.Text(String(convertPxToMm(shapeObject.width)) + "mm", {

							fontSize: fontSize,

							originX: 'center',

							fill: 'black',

							top: - ( shape.height / 2 ) + defaultGap

						});



						var invertedUEdgeProfile1 = jQuery('select[name="UEdgeProfile1"]').val();

						if (invertedUEdgeProfile1 != '' && invertedUEdgeProfile1 != '0') {

							var topEdgeProfileText = jQuery('select[name="UEdgeProfile1"] option:selected').text();

						} else {

							var topEdgeProfileText = 'Unfinished';

						}



						var topEdgeProfile = new fabric.Text(topEdgeProfileText, {

							fontSize: topText.fontSize,

							originX: topText.originX,

							fill: topText.fill,

							top: topText.top + fontSize

						});



						// Right edge

						var rightText = new fabric.Text(String(Math.round(convertPxToMm(shapeObject.height * 0.5))) + "mm", {

							fontSize: fontSize,

							fill: 'black',

							angle: 90,

							top: - fontSize - defaultGap,

							left: ( shape.width / 2 ) - defaultGap,

						});



						var invertedUEdgeProfile2 = jQuery('select[name="UEdgeProfile2"]').val();

						if (invertedUEdgeProfile2 != '' && invertedUEdgeProfile2 != '0') {

							var rightEdgeProfileText = jQuery('select[name="UEdgeProfile2"] option:selected').text();

						} else {

							var rightEdgeProfileText = 'Unfinished';

						}



						var rightEdgeProfile = new fabric.Text(rightEdgeProfileText, {

							fontSize: rightText.fontSize,

							fill: rightText.fill,

							angle: rightText.angle,

							left: rightText.left - fontSize,

							top: rightText.top

						});



						// Left edge

						var leftText = new fabric.Text(String(Math.round(convertPxToMm(shapeObject.height * 0.5))) + "mm", {

							fontSize: fontSize,

							fill: 'black',

							angle: -90,

							top: fontSize + defaultGap,

							left: - ( shape.width / 2 ) + defaultGap,

						});



						var invertedUEdgeProfile3 = jQuery('select[name="UEdgeProfile3"]').val();

						if (invertedUEdgeProfile3 != '' && invertedUEdgeProfile3 != '0') {

							var leftEdgeProfileText = jQuery('select[name="UEdgeProfile3"] option:selected').text();

						} else {

							var leftEdgeProfileText = 'Unfinished';

						}



						var leftEdgeProfile = new fabric.Text(leftEdgeProfileText, {

							fontSize: leftText.fontSize,

							fill: leftText.fill,

							angle: leftText.angle,

							left: leftText.left + fontSize,

							top: leftText.top

						});



						// Bottom left edge

						var bottomLeftText = new fabric.Text(String(Math.round(convertPxToMm(shapeObject.width * 0.2))) + "mm", {

							fontSize: fontSize,

							originX: 'center',

							fill: 'black',

							top: ( shape.height / 2 ) - fontSize - defaultGap

						});



						var invertedUEdgeProfile4 = jQuery('select[name="UEdgeProfile4"]').val();

						if (invertedUEdgeProfile4 != '' && invertedUEdgeProfile4 != '0') {

							var bottomLeftEdgeProfileText = jQuery('select[name="UEdgeProfile4"] option:selected').text();

						} else {

							var bottomLeftEdgeProfileText = 'Unfinished';

						}



						var bottomLeftEdgeProfile = new fabric.Text(bottomLeftEdgeProfileText, {

							fontSize: bottomLeftText.fontSize,

							originX: bottomLeftText.originX,

							fill: bottomLeftText.fill,

							top: bottomLeftText.top - fontSize

						});



						// Bottom right edge

						var bottomRightText = new fabric.Text(String(Math.round(convertPxToMm(shapeObject.width * 0.2))) + "mm", {

							fontSize: fontSize,

							originX: 'center',

							fill: 'black',

							top: ( shape.height / 2 ) - fontSize - defaultGap

						});



						var invertedUEdgeProfile5 = jQuery('select[name="UEdgeProfile5"]').val();

						if (invertedUEdgeProfile5 != '' && invertedUEdgeProfile5 != '0') {

							var bottomRightEdgeProfileText = jQuery('select[name="UEdgeProfile5"] option:selected').text();

						} else {

							var bottomRightEdgeProfileText = 'Unfinished';

						}



						var bottomRightEdgeProfile = new fabric.Text(bottomRightEdgeProfileText, {

							fontSize: bottomRightText.fontSize,

							originX: bottomRightText.originX,

							fill: bottomRightText.fill,

							top: bottomRightText.top - fontSize

						});



						// Inside top edge

						var insideTopText = new fabric.Text(String(Math.round(convertPxToMm(shapeObject.height * 0.2))) + "mm", {

							fontSize: fontSize,

							originX: 'center',

							fill: 'black',

							top: ( shape.height / 2 ) - fontSize - defaultGap

						});



						var invertedUEdgeProfile6 = jQuery('select[name="UEdgeProfile6"]').val();

						if (invertedUEdgeProfile6 != '' && invertedUEdgeProfile6 != '0') {

							var insideTopEdgeProfileText = jQuery('select[name="UEdgeProfile6"] option:selected').text();

						} else {

							var insideTopEdgeProfileText = 'Unfinished';

						}



						var insideTopEdgeProfile = new fabric.Text(insideTopEdgeProfileText, {

							fontSize: insideTopText.fontSize,

							originX: insideTopText.originX,

							fill: insideTopText.fill,

							top: insideTopText.top - fontSize

						});



						groupArr.push(topText);

						groupArr.push(topEdgeProfile);

						groupArr.push(rightText);

						groupArr.push(rightEdgeProfile);

						groupArr.push(leftText);

						groupArr.push(leftEdgeProfile);

						groupArr.push(bottomLeftText);

						groupArr.push(bottomLeftEdgeProfile);

						groupArr.push(bottomRightText);

						groupArr.push(bottomRightEdgeProfile);

						groupArr.push(insideTopText);

						groupArr.push(insideTopEdgeProfile);

											} else if ( shapeObject.type == 'shape-3' ) {

						// Top edge

						var topText = new fabric.Text(String(convertPxToMm(shapeObject.width)) + "mm", {

							fontSize: fontSize,

							originX: 'center',

							fill: 'black',

							top: - ( shape.height / 2 ) + defaultGap

						});



						var shape_3EdgeProfile1 = jQuery('select[name="shape-3EdgeProfile1"]').val();

						if (shape_3EdgeProfile1 != '' && shape_3EdgeProfile1 != '0') {

							var topEdgeProfileText = jQuery('select[name="shape-3EdgeProfile1"] option:selected').text();

						} else {

							var topEdgeProfileText = 'Unfinished';

						}



						var topEdgeProfile = new fabric.Text(topEdgeProfileText, {

							fontSize: topText.fontSize,

							originX: topText.originX,

							fill: topText.fill,

							top: topText.top + fontSize

						});



						// Right edge

						var rightText = new fabric.Text(String(convertPxToMm(shapeObject.height)) + "mm", {

							fontSize: fontSize,

							fill: 'black',

							angle: 90,

							top: - fontSize - defaultGap,

							left: ( shape.width / 2 ) - defaultGap,

						});



						var shape_3EdgeProfile2 = jQuery('select[name="shape-3EdgeProfile2"]').val();

						if (shape_3EdgeProfile2 != '' && shape_3EdgeProfile2 != '0') {

							var rightEdgeProfileText = jQuery('select[name="shape-3EdgeProfile2"] option:selected').text();

						} else {

							var rightEdgeProfileText = 'Unfinished';

						}



						var rightEdgeProfile = new fabric.Text(rightEdgeProfileText, {

							fontSize: rightText.fontSize,

							fill: rightText.fill,

							angle: rightText.angle,

							left: rightText.left - fontSize,

							top: rightText.top

						});



						// Bottom edge

						var bottomText = new fabric.Text(String(convertPxToMm(shapeObject.width)) + "mm", {

							fontSize: fontSize,

							originX: 'center',

							fill: 'black',

							top: ( shape.height / 2 ) - fontSize - defaultGap

						});



						var shape_3EdgeProfile3 = jQuery('select[name="shape-3EdgeProfile3"]').val();

						if (shape_3EdgeProfile3 != '' && shape_3EdgeProfile3 != '0') {

							var bottomEdgeProfileText = jQuery('select[name="shape-3EdgeProfile3"] option:selected').text();

						} else {

							var bottomEdgeProfileText = 'Unfinished';

						}



						var bottomEdgeProfile = new fabric.Text(bottomEdgeProfileText, {

							fontSize: bottomText.fontSize,

							originX: bottomText.originX,

							fill: bottomText.fill,

							top: bottomText.top - fontSize

						});



						// Right bottom edge

						var rightBottomText = new fabric.Text(String(Math.round(convertPxToMm(shapeObject.height * 0.2))) + "mm", {

							fontSize: fontSize,

							fill: 'black',

							angle: 90,

							top: ( shape.height / 2 ) + fontSize + defaultGap,

							left: ( shape.width / 2 ) + defaultGap,

						});



						var shape_3EdgeProfile4 = jQuery('select[name="shape-3EdgeProfile4"]').val();

						if (shape_3EdgeProfile4 != '' && shape_3EdgeProfile4 != '0') {

							var rightBottomEdgeProfileText = jQuery('select[name="shape-3EdgeProfile4"] option:selected').text();

						} else {

							var rightBottomEdgeProfileText = 'Unfinished';

						}



						var rightBottomEdgeProfile = new fabric.Text(rightBottomEdgeProfileText, {

							fontSize: rightBottomText.fontSize,

							fill: rightBottomText.fill,

							angle: rightBottomText.angle,

							left: rightBottomText.left + fontSize,

							top: rightBottomText.top

						});



						groupArr.push(topText);

						groupArr.push(topEdgeProfile);

						groupArr.push(rightText);

						groupArr.push(rightEdgeProfile);

						groupArr.push(bottomText);

						groupArr.push(bottomEdgeProfile);

						groupArr.push(rightBottomText);

						groupArr.push(rightBottomEdgeProfile);

					} else if ( shapeObject.type == 'L' ) {

						// Top edge

						var topText = new fabric.Text(String(Math.round(convertPxToMm(shapeObject.width * 0.3))) + "mm", {

							fontSize: fontSize,

							originX: 'center',

							fill: 'black',

							top: - ( shape.height / 2 ) + defaultGap

						});



						var lEdgeProfile1 = jQuery('select[name="LEdgeProfile1"]').val();

						if (lEdgeProfile1 != '' && lEdgeProfile1 != '0') {

							var topEdgeProfileText = jQuery('select[name="LEdgeProfile1"] option:selected').text();

						} else {

							var topEdgeProfileText = 'Unfinished';

						}



						var topEdgeProfile = new fabric.Text(topEdgeProfileText, {

							fontSize: topText.fontSize,

							originX: topText.originX,

							fill: topText.fill,

							top: topText.top + fontSize

						});



						// Right edge

						var rightText = new fabric.Text(String(Math.round(convertPxToMm(shapeObject.height * 0.3))) + "mm", {

							fontSize: fontSize,

							fill: 'black',

							angle: 90,

							top: - fontSize - defaultGap,

							left: ( shape.width / 2 ) - defaultGap,

						});



						var lEdgeProfile2 = jQuery('select[name="LEdgeProfile2"]').val();

						if (lEdgeProfile2 != '' && lEdgeProfile2 != '0') {

							var rightEdgeProfileText = jQuery('select[name="LEdgeProfile2"] option:selected').text();

						} else {

							var rightEdgeProfileText = 'Unfinished';

						}



						var rightEdgeProfile = new fabric.Text(rightEdgeProfileText, {

							fontSize: rightText.fontSize,

							fill: rightText.fill,

							angle: rightText.angle,

							left: rightText.left - fontSize,

							top: rightText.top

						});



						groupArr.push(topText);

						groupArr.push(topEdgeProfile);

						groupArr.push(rightText);

						groupArr.push(rightEdgeProfile);

					}

                       





					// Create a group consisting of the shape and the text

					var groupObj = {

						lockScalingX: true,

						lockScalingY: true,

					};



					if ( shapeObject.hasOwnProperty('x') ) {

						groupObj.left = shapeObject.x;

					}



					if ( shapeObject.hasOwnProperty('y') ) {

						groupObj.top = shapeObject.y;

					}





					var group = new fabric.Group(groupArr, groupObj);



					// Add the group to the canvas

					canvas.add(group);

					shape.setCoords();

					

					// Ensure watermarks stay at the back

					ensureWatermarksAtBack();

					

					canvas.requestRenderAll();

					

					// Expand canvas and update scrollbars when new shapes are added

					setTimeout(() => {

						expandCanvasIfNeeded();

						updateScrollbars();

						// Update slab usage calculation and visualization

						calculateSlabUsage();

						updateSlabVisualization();

					}, 100);



					return group;

				}





				// Event listener for shapes dropdown button

				console.log('Setting up shapes dropdown button');

				var shapesButton = jQuery('#toolbar .shapes .shapes-dropdown-btn');

				console.log('Shapes button found:', shapesButton.length);

				

				shapesButton.click(function(e) {

					e.preventDefault();

					e.stopPropagation();

					console.log('Shapes dropdown clicked');

					var dropdown = jQuery(this).next('.shapes-dropdown');

					console.log('Dropdown element:', dropdown.length);

					console.log('Dropdown display before:', dropdown.css('display'));

					console.log('Dropdown position:', dropdown.position());

					console.log('Dropdown offset:', dropdown.offset());

					

					// Toggle dropdown

					dropdown.slideToggle();

					

					// Toggle active class on button

					jQuery(this).toggleClass('active');

					

					console.log('Dropdown display after:', dropdown.css('display'));

				});



				// Event listener for shapes dropdown items

				jQuery('body').on('click', '#toolbar .shapes .shapes-dropdown li', function() {

					var shapeType = jQuery(this).data('shape');

					resetShapeModal();

					if ( shapeType == 'circle' ) {

						jQuery('#shapeModal .form-group #shapeWidth').parent().hide();

					}



					jQuery('#shapeModal .form-group .edge-profile-section > div.' + shapeType).show();

					jQuery('#shapeModal').css('display', 'flex').attr('data-shape', shapeType);

					

					// Hide the dropdown after selection

					jQuery('#toolbar .shapes .shapes-dropdown').slideUp();

				});



				// Close shapes dropdown when clicking outside

				jQuery('body').on('click', function(e) {

					if (!jQuery(e.target).closest('#toolbar .shapes').length) {

						jQuery('#toolbar .shapes .shapes-dropdown').slideUp();

						jQuery('#toolbar .shapes .shapes-dropdown-btn').removeClass('active');

					}

				});



				// Hide Modal

				jQuery('body').on('click', '.modal #cancel', function() {

					resetShapeModal();

					jQuery(this).parent().parent().hide();

				});





				// Save shape details when the 'Save' button is clicked

				jQuery('body').on('click', '#shapeModal #saveShapeDetails', function() {



					var state = jQuery(this).attr('data-state');

					var editX = defaultGap;

					var editY = defaultGap;



					if ( state == 'edit' ) {

						var activeObject = canvas.getActiveObject();

						if ( activeObject ) {

							editX = activeObject.left;

							editY = activeObject.top;

							if (activeObject.type === 'group') {

								// Ungroup all the objects from the group

								activeObject.forEachObject(function(object) {

									canvas.remove(object);  // Remove each child object from the canvas

								});

							}

							canvas.remove(activeObject);

							canvas.renderAll(); // Re-render the canvas
							
							// Update slab usage calculation and visualization
							calculateSlabUsage();
							updateSlabVisualization();

							jQuery(this).attr('data-state', 'save');

						} else {

							jQuery('#shapeModal').hide();

						}



					}



					var shapeType = jQuery('#shapeModal').attr('data-shape');



					var width = convertMmToPx(parseFloat(jQuery('#shapeModal #shapeWidth').val()));

					var height = convertMmToPx(parseFloat(jQuery('#shapeModal #shapeHeight').val()));

					var name = jQuery('#shapeModal #shapeName').val() || 'Enter Name';

					var shapesList = [];



					// Hide the modal

					jQuery('#shapeModal').hide();

					

					// Default Shape

					var group = createShape(

						{

							x: defaultGap,

							y: defaultGap,

							width: width,

							height: height,

							type: shapeType,

							text: name

						}

					);



					shapesList.push(group);

					

					var shape;

					switch(shapeType) {

						case 'square':

						case 'rounded-square':

							// Top corner

							var squareEdgeProfile1 = jQuery('select[name="'+ shapeType +'EdgeProfile1"]').val();

							var topEdgeProfileHeight = jQuery('select[name="'+ shapeType +'EdgeProfile1"] option:selected').data('size');

							if (squareEdgeProfile1 != '' && squareEdgeProfile1 != '0' && topEdgeProfileHeight != '0') {

								var topEdgeProfileText = jQuery('select[name="'+ shapeType +'EdgeProfile1"] option:selected').text();

								shapesList.push(createEdgeProfileShape(

									{

										x: group.left,

										y: group.top - defaultGap - topEdgeProfileHeight,

										width: width,

										height: topEdgeProfileHeight,

										name: shapeType +'EdgeProfile1',

										text: topEdgeProfileText,

										value: squareEdgeProfile1,

									}

								));

							}

							

							// Right corner

							var squareEdgeProfile2 = jQuery('select[name="'+ shapeType +'EdgeProfile2"]').val();

							var rightEdgeProfileWidth = jQuery('select[name="'+ shapeType +'EdgeProfile2"] option:selected').data('size');

							if (squareEdgeProfile2 != '' && squareEdgeProfile2 != '0' && rightEdgeProfileWidth != '0') {

								var rightEdgeProfileText = jQuery('select[name="'+ shapeType +'EdgeProfile2"] option:selected').text();

								shapesList.push(createEdgeProfileShape(

									{

										x: group.left + width + defaultGap,

										y: group.top,

										width: rightEdgeProfileWidth,

										height: height,

										name: shapeType +'EdgeProfile2',

										text: rightEdgeProfileText,

										value: squareEdgeProfile2,

									}

								));

							}

							

							

							// Bottom corner

							var squareEdgeProfile3 = jQuery('select[name="'+ shapeType +'EdgeProfile3"]').val();

							var bottomEdgeProfileWidth = jQuery('select[name="'+ shapeType +'EdgeProfile3"] option:selected').data('size');

							if (squareEdgeProfile3 != '' && squareEdgeProfile3 != '0' && bottomEdgeProfileWidth != '0') {

								var bottomEdgeProfileText = jQuery('select[name="'+ shapeType +'EdgeProfile3"] option:selected').text();

								shapesList.push(createEdgeProfileShape(

									{

										x: group.left,

										y: group.top + height + defaultGap,

										width: width,

										height: bottomEdgeProfileWidth,

										name: shapeType +'EdgeProfile3',

										text: bottomEdgeProfileText,

										value: squareEdgeProfile3,

									}

								));

							}



							// Left corner

							var squareEdgeProfile4 = jQuery('select[name="'+ shapeType +'EdgeProfile4"]').val();

							var leftEdgeProfileHeight = jQuery('select[name="'+ shapeType +'EdgeProfile4"] option:selected').data('size');

							if (squareEdgeProfile4 != '' && squareEdgeProfile4 != '0' && leftEdgeProfileHeight != '0') {

								var leftEdgeProfileText = jQuery('select[name="'+ shapeType +'EdgeProfile4"] option:selected').text();

								shapesList.push(createEdgeProfileShape(

									{

										x: group.left - defaultGap - leftEdgeProfileHeight,

										y: group.top,

										width: leftEdgeProfileHeight,

										height: height,

										name: shapeType +'EdgeProfile4',

										text: leftEdgeProfileText,

										value: squareEdgeProfile4,

									}

								));

							}

							break;

						case 'U':

							// Edge Profiles for inverted U (Π)

							(function(){

								var bar = Math.max(4, Math.round(Math.min(width, height) * 0.10));

								var gapRatio = 0.26; // opening width

								var depthRatio = 0.70; // opening depth



								var gapWidth = Math.round(width * gapRatio);

								var notchDepth = Math.round(height * depthRatio);

								var gapLeft = Math.round((width - gapWidth) / 2);

								var gapRight = gapLeft + gapWidth;



								// 1: Top edge (full)

								var u1 = jQuery('select[name="UEdgeProfile1"]').val();

								var u1Size = jQuery('select[name="UEdgeProfile1"] option:selected').data('size');

								if (u1 && u1 !== '0' && u1Size && u1Size !== '0') {

									var u1Text = jQuery('select[name="UEdgeProfile1"] option:selected').text();

									shapesList.push(createEdgeProfileShape({

										x: group.left,

										y: group.top - defaultGap - u1Size,

										width: width,

										height: u1Size,

										name: 'UEdgeProfile1',

										text: u1Text,

										value: u1

									}));

								}



								// 2: Right outer edge (full)

								var u2 = jQuery('select[name="UEdgeProfile2"]').val();

								var u2Size = jQuery('select[name="UEdgeProfile2"] option:selected').data('size');

								if (u2 && u2 !== '0' && u2Size && u2Size !== '0') {

									var u2Text = jQuery('select[name="UEdgeProfile2"] option:selected').text();

									shapesList.push(createEdgeProfileShape({

										x: group.left + width + defaultGap,

										y: group.top,

										width: u2Size,

										height: height,

										name: 'UEdgeProfile2',

										text: u2Text,

										value: u2

									}));

								}



								// 3: Bottom-right segment

								var u3 = jQuery('select[name="UEdgeProfile3"]').val();

								var u3Size = jQuery('select[name="UEdgeProfile3"] option:selected').data('size');

								if (u3 && u3 !== '0' && u3Size && u3Size !== '0') {

									var u3Text = jQuery('select[name="UEdgeProfile3"] option:selected').text();

									shapesList.push(createEdgeProfileShape({

										x: group.left + gapRight,

										y: group.top + height + defaultGap,

										width: width - gapRight,

										height: u3Size,

										name: 'UEdgeProfile3',

										text: u3Text,

										value: u3

									}));

								}



								// 4: Bottom-left segment

								var u4 = jQuery('select[name="UEdgeProfile4"]').val();

								var u4Size = jQuery('select[name="UEdgeProfile4"] option:selected').data('size');

								if (u4 && u4 !== '0' && u4Size && u4Size !== '0') {

									var u4Text = jQuery('select[name="UEdgeProfile4"] option:selected').text();

									shapesList.push(createEdgeProfileShape({

										x: group.left,

										y: group.top + height + defaultGap,

										width: gapLeft,

										height: u4Size,

										name: 'UEdgeProfile4',

										text: u4Text,

										value: u4

									}));

								}



								// 5: Left outer edge (full)

								var u5 = jQuery('select[name="UEdgeProfile5"]').val();

								var u5Size = jQuery('select[name="UEdgeProfile5"] option:selected').data('size');

								if (u5 && u5 !== '0' && u5Size && u5Size !== '0') {

									var u5Text = jQuery('select[name="UEdgeProfile5"] option:selected').text();

									shapesList.push(createEdgeProfileShape({

										x: group.left - defaultGap - u5Size,

										y: group.top,

										width: u5Size,

										height: height,

										name: 'UEdgeProfile5',

										text: u5Text,

										value: u5

									}));

								}



								// 6: Inside top (over the notch)

								var u6 = jQuery('select[name="UEdgeProfile6"]').val();

								var u6Size = jQuery('select[name="UEdgeProfile6"] option:selected').data('size');

								if (u6 && u6 !== '0' && u6Size && u6Size !== '0') {

									var u6Text = jQuery('select[name="UEdgeProfile6"] option:selected').text();

									shapesList.push(createEdgeProfileShape({

										x: group.left + gapLeft,

										y: group.top + (height - notchDepth) - defaultGap - u6Size,

										width: gapWidth,

										height: u6Size,

										name: 'UEdgeProfile6',

										text: u6Text,

										value: u6

									}));

								}

							})();

							break;

						case 'L':

							(function(){

								// Basic dimensions and split helpers

								var halfH = Math.round(height / 2);

								var leftSegW = Math.round(width * 0.35);

								var rightSegW = width - leftSegW;



								// 1: Top edge (full)

								var l1 = jQuery('select[name="LEdgeProfile1"]').val();

								var l1Size = jQuery('select[name="LEdgeProfile1"] option:selected').data('size');

								if (l1 && l1 !== '0' && l1Size && l1Size !== '0') {

									var l1Text = jQuery('select[name="LEdgeProfile1"] option:selected').text();

									shapesList.push(createEdgeProfileShape({ x: group.left, y: group.top - defaultGap - l1Size, width: width, height: l1Size, name: 'LEdgeProfile1', text: l1Text, value: l1 }));

								}



								// 2: Right edge (upper half)

								var l2 = jQuery('select[name="LEdgeProfile2"]').val();

								var l2Size = jQuery('select[name="LEdgeProfile2"] option:selected').data('size');

								if (l2 && l2 !== '0' && l2Size && l2Size !== '0') {

									var l2Text = jQuery('select[name="LEdgeProfile2"] option:selected').text();

									shapesList.push(createEdgeProfileShape({ x: group.left + width + defaultGap, y: group.top, width: l2Size, height: halfH, name: 'LEdgeProfile2', text: l2Text, value: l2 }));

								}



								// 3: Right edge (lower half)

								var l3 = jQuery('select[name="LEdgeProfile3"]').val();

								var l3Size = jQuery('select[name="LEdgeProfile3"] option:selected').data('size');

								if (l3 && l3 !== '0' && l3Size && l3Size !== '0') {

									var l3Text = jQuery('select[name="LEdgeProfile3"] option:selected').text();

									shapesList.push(createEdgeProfileShape({ x: group.left + width + defaultGap, y: group.top + halfH, width: l3Size, height: height - halfH, name: 'LEdgeProfile3', text: l3Text, value: l3 }));

								}



								// 4: Bottom (right segment)

								var l4 = jQuery('select[name="LEdgeProfile4"]').val();

								var l4Size = jQuery('select[name="LEdgeProfile4"] option:selected').data('size');

								if (l4 && l4 !== '0' && l4Size && l4Size !== '0') {

									var l4Text = jQuery('select[name="LEdgeProfile4"] option:selected').text();

									shapesList.push(createEdgeProfileShape({ x: group.left + leftSegW, y: group.top + height + defaultGap, width: rightSegW, height: l4Size, name: 'LEdgeProfile4', text: l4Text, value: l4 }));

								}



								// 5: Bottom (left segment)

								var l5 = jQuery('select[name="LEdgeProfile5"]').val();

								var l5Size = jQuery('select[name="LEdgeProfile5"] option:selected').data('size');

								if (l5 && l5 !== '0' && l5Size && l5Size !== '0') {

									var l5Text = jQuery('select[name="LEdgeProfile5"] option:selected').text();

									shapesList.push(createEdgeProfileShape({ x: group.left, y: group.top + height + defaultGap, width: leftSegW, height: l5Size, name: 'LEdgeProfile5', text: l5Text, value: l5 }));

								}



								// 6: Left edge (full)

								var l6 = jQuery('select[name="LEdgeProfile6"]').val();

								var l6Size = jQuery('select[name="LEdgeProfile6"] option:selected').data('size');

								if (l6 && l6 !== '0' && l6Size && l6Size !== '0') {

									var l6Text = jQuery('select[name="LEdgeProfile6"] option:selected').text();

									shapesList.push(createEdgeProfileShape({ x: group.left - defaultGap - l6Size, y: group.top, width: l6Size, height: height, name: 'LEdgeProfile6', text: l6Text, value: l6 }));

								}

							})();

							break;



						case 'shape-3':

							(function(){

								// Treat shape-3 (trapezoid) with 4 sides

								var s1 = jQuery('select[name="shape-3EdgeProfile1"]').val();

								var s1Size = jQuery('select[name="shape-3EdgeProfile1"] option:selected').data('size');

								if (s1 && s1 !== '0' && s1Size && s1Size !== '0') {

									var s1Text = jQuery('select[name="shape-3EdgeProfile1"] option:selected').text();

									shapesList.push(createEdgeProfileShape({ x: group.left, y: group.top - defaultGap - s1Size, width: width, height: s1Size, name: 'shape-3EdgeProfile1', text: s1Text, value: s1 }));

								}



								var s2 = jQuery('select[name="shape-3EdgeProfile2"]').val();

								var s2Size = jQuery('select[name="shape-3EdgeProfile2"] option:selected').data('size');

								if (s2 && s2 !== '0' && s2Size && s2Size !== '0') {

									var s2Text = jQuery('select[name="shape-3EdgeProfile2"] option:selected').text();

									shapesList.push(createEdgeProfileShape({ x: group.left + width + defaultGap, y: group.top, width: s2Size, height: height, name: 'shape-3EdgeProfile2', text: s2Text, value: s2 }));

								}



								var s3 = jQuery('select[name="shape-3EdgeProfile3"]').val();

								var s3Size = jQuery('select[name="shape-3EdgeProfile3"] option:selected').data('size');

								if (s3 && s3 !== '0' && s3Size && s3Size !== '0') {

									var s3Text = jQuery('select[name="shape-3EdgeProfile3"] option:selected').text();

									shapesList.push(createEdgeProfileShape({ x: group.left, y: group.top + height + defaultGap, width: width, height: s3Size, name: 'shape-3EdgeProfile3', text: s3Text, value: s3 }));

								}



								var s4 = jQuery('select[name="shape-3EdgeProfile4"]').val();

								var s4Size = jQuery('select[name="shape-3EdgeProfile4"] option:selected').data('size');

								if (s4 && s4 !== '0' && s4Size && s4Size !== '0') {

									var s4Text = jQuery('select[name="shape-3EdgeProfile4"] option:selected').text();

									shapesList.push(createEdgeProfileShape({ x: group.left - defaultGap - s4Size, y: group.top, width: s4Size, height: height, name: 'shape-3EdgeProfile4', text: s4Text, value: s4 }));

								}

							})();

							break;

						case 'circle':

							var circleEdgeProfile1 = jQuery('select[name="circleEdgeProfile1"]').val();

							var allEdgeProfileHeight = jQuery('select[name="circleEdgeProfile1"] option:selected').data('size');

							if (circleEdgeProfile1 != '' && circleEdgeProfile1 != '0' && allEdgeProfileHeight != '0') {

								var circleEdgeProfileText = jQuery('select[name="circleEdgeProfile1"] option:selected').text();

								shapesList.unshift(createEdgeProfileShape(

									{

										width: height + defaultGap,

										height: height + defaultGap,

										name: 'circleEdgeProfile1',

										text: circleEdgeProfileText,

										value: circleEdgeProfile1,

										type: shapeType,

									}

								));



								shapesList.unshift(createEdgeProfileShape(

									{

										width: height + allEdgeProfileHeight + defaultGap,

										height: height + allEdgeProfileHeight + defaultGap,

										name: 'circleEdgeProfile1',

										text: circleEdgeProfileText,

										value: circleEdgeProfile1,

										type: shapeType,

									}

								));

							}

							break;

						case 'ellipse':

							var ellipseEdgeProfile1 = jQuery('select[name="ellipseEdgeProfile1"]').val();

							var allEdgeProfileHeight = jQuery('select[name="ellipseEdgeProfile1"] option:selected').data('size');

							if (ellipseEdgeProfile1 != '' && ellipseEdgeProfile1 != '0' && allEdgeProfileHeight != '0') {

								var ellipseEdgeProfileText = jQuery('select[name="ellipseEdgeProfile1"] option:selected').text();

								shapesList.unshift(createEdgeProfileShape(

									{

										width: width + defaultGap,

										height: height + defaultGap,

										name: 'ellipseEdgeProfile1',

										text: ellipseEdgeProfileText,

										value: ellipseEdgeProfile1,

										type: shapeType,

									}

								));



								shapesList.unshift(createEdgeProfileShape(

									{

										width: width + allEdgeProfileHeight + defaultGap,

										height: height + allEdgeProfileHeight + defaultGap,

										name: 'ellipseEdgeProfile1',

										text: ellipseEdgeProfileText,

										value: ellipseEdgeProfile1,

										type: shapeType,

									}

								));

							}

							break;

						case 'polygon':

							// Top corner

							var polygonEdgeProfile1 = jQuery('select[name="polygonEdgeProfile1"]').val();

							var topEdgeProfileHeight = jQuery('select[name="polygonEdgeProfile1"] option:selected').data('size');

							if ( polygonEdgeProfile1 != '' && polygonEdgeProfile1 != '0' && topEdgeProfileHeight != '0' ) {

								var topEdgeProfileText = jQuery('select[name="polygonEdgeProfile1"] option:selected').text();

								shapesList.push(createEdgeProfileShape(

									{

										x: group.left + ( group.width / 4 ),

										y: group.top - defaultGap - topEdgeProfileHeight,

										width: ( group.width / 2 ),

										height: topEdgeProfileHeight,

										name: 'polygonEdgeProfile1',

										text: topEdgeProfileText,

										value: polygonEdgeProfile1,

									}

								));



							}



							// Top-right corner

							var polygonEdgeProfile2 = jQuery('select[name="polygonEdgeProfile2"]').val();

							var topRightEdgeProfileWidth = jQuery('select[name="polygonEdgeProfile2"] option:selected').data('size');

							if ( polygonEdgeProfile2 != '' && polygonEdgeProfile2 != '0' && topRightEdgeProfileWidth != '0' ) {

								var topRightEdgeProfileText = jQuery('select[name="polygonEdgeProfile2"] option:selected').text();

								shapesList.push(createEdgeProfileShape(

									{

										x: group.left + ( group.width - ( group.width / 4 ) ) + defaultGap,

										y: group.top,

										width: topRightEdgeProfileWidth,

										height: convertMmToPx(parseInt(group._objects[4].text.replace('mm', ''))),

										angle: group._objects[4].customAngle,

										name: 'polygonEdgeProfile2',

										text: topRightEdgeProfileText,

										value: polygonEdgeProfile2,

									}

								));

							}



							// Bottom right corner

							var polygonEdgeProfile3 = jQuery('select[name="polygonEdgeProfile3"]').val();

							var bottomRightEdgeProfileWidth = jQuery('select[name="polygonEdgeProfile3"] option:selected').data('size');

							if ( polygonEdgeProfile3 != '' && polygonEdgeProfile3 != '0' && bottomRightEdgeProfileWidth != '0' ) {

								var bottomRightEdgeProfileText = jQuery('select[name="polygonEdgeProfile3"] option:selected').text();

								shapesList.push(createEdgeProfileShape(

									{

										x: group.left + group.width + defaultGap,

										y: group.top + ( group.height / 2 ),

										width: bottomRightEdgeProfileWidth,

										height: convertMmToPx(parseInt(group._objects[6].text.replace('mm', ''))),

										angle: group._objects[6].customAngle,

										name: 'polygonEdgeProfile3',

										text: bottomRightEdgeProfileText,

										value: polygonEdgeProfile3,

									}

								));

							}



							// Bottom corner

							var polygonEdgeProfile4 = jQuery('select[name="polygonEdgeProfile4"]').val();

							var bottomEdgeProfileHeight = jQuery('select[name="polygonEdgeProfile4"] option:selected').data('size');

							if ( polygonEdgeProfile4 != '' && polygonEdgeProfile4 != '0' && bottomEdgeProfileHeight != '0' ) {

								var bottomEdgeProfileText = jQuery('select[name="polygonEdgeProfile4"] option:selected').text();

								shapesList.push(createEdgeProfileShape(

									{

										x: group.left + ( group.width / 4 ),

										y: group.top + group.height + defaultGap,

										width: ( group.width / 2 ),

										height: bottomEdgeProfileHeight,

										name: 'polygonEdgeProfile4',

										text: bottomEdgeProfileText,

										value: polygonEdgeProfile4,

									}

								));

							}



							// Bottom-left corner

							var polygonEdgeProfile5 = jQuery('select[name="polygonEdgeProfile5"]').val();

							var bottomLeftEdgeProfileHeight = jQuery('select[name="polygonEdgeProfile5"] option:selected').data('size');

							if ( polygonEdgeProfile5 != '' && polygonEdgeProfile5 != '0' && bottomLeftEdgeProfileHeight != '0' ) {

								var bottomLeftEdgeProfileText = jQuery('select[name="polygonEdgeProfile5"] option:selected').text();

								shapesList.push(createEdgeProfileShape(

									{

										x: group.left - defaultGap - bottomLeftEdgeProfileHeight,

										y: group.top + ( group.height / 2 ) + ( bottomLeftEdgeProfileHeight / 2 ),

										width: bottomLeftEdgeProfileHeight,

										height: convertMmToPx(parseInt(group._objects[10].text.replace('mm', ''))),

										angle: group._objects[10].customAngle,

										name: 'polygonEdgeProfile5',

										text: bottomLeftEdgeProfileText,

										value: polygonEdgeProfile5,

									}

								));

							}



							// Top-left corner

							var polygonEdgeProfile6 = jQuery('select[name="polygonEdgeProfile6"]').val();

							var topLeftEdgeProfileHeight = jQuery('select[name="polygonEdgeProfile6"] option:selected').data('size');

							if ( polygonEdgeProfile6 != '' && polygonEdgeProfile6 != '0' && topLeftEdgeProfileHeight != '0' ) {

								var topLeftEdgeProfileText = jQuery('select[name="polygonEdgeProfile6"] option:selected').text();

								shapesList.push(createEdgeProfileShape(

									{

										x: group.left + ( group.width / 4 ) - defaultGap - topLeftEdgeProfileHeight,

										y: group.top - ( topLeftEdgeProfileHeight / 2 ),

										width: topLeftEdgeProfileHeight,

										height: convertMmToPx(parseInt(group._objects[12].text.replace('mm', ''))),

										angle: group._objects[12].customAngle,

										name: 'polygonEdgeProfile6',

										text: topLeftEdgeProfileText,

										value: polygonEdgeProfile6,

									}

								));

							}



							break;

						case 'triangle':

							// Top-Right corner

							var triangleEdgeProfile1 = jQuery('select[name="triangleEdgeProfile1"]').val();

							var topRightEdgeProfileHeight = jQuery('select[name="triangleEdgeProfile1"] option:selected').data('size');

							if ( triangleEdgeProfile1 != '' && triangleEdgeProfile1 != '0' && topRightEdgeProfileHeight != '0' ) {

								var topRightEdgeProfileText = jQuery('select[name="triangleEdgeProfile1"] option:selected').text();



								shapesList.push(createEdgeProfileShape(

									{

										x: group.left + ( group.width / 2 ) + defaultGap,

										y: group.top,

										width: topRightEdgeProfileHeight,

										height: convertMmToPx(parseInt(group._objects[2].text.replace('mm', ''))),

										name: 'triangleEdgeProfile1',

										text: topRightEdgeProfileText,

										value: triangleEdgeProfile1,

										angle: group._objects[2].customAngle

									}

								));



							}



							// Bottom corner

							var triangleEdgeProfile2 = jQuery('select[name="triangleEdgeProfile2"]').val();

							var bottomEdgeProfileWidth = jQuery('select[name="triangleEdgeProfile2"] option:selected').data('size');

							if ( triangleEdgeProfile2 != '' && triangleEdgeProfile2 != '0' && bottomEdgeProfileWidth != '0' ) {

								var bottomEdgeProfileText = jQuery('select[name="triangleEdgeProfile2"] option:selected').text();

								shapesList.push(createEdgeProfileShape(

									{

										x: group.left,

										y: group.top + height + defaultGap,

										width: width,

										height: bottomEdgeProfileWidth,

										name: 'triangleEdgeProfile2',

										text: bottomEdgeProfileText,

										value: triangleEdgeProfile2,

									}

								));

							}



							// Top-left corner

							var triangleEdgeProfile3 = jQuery('select[name="triangleEdgeProfile3"]').val();

							var topLeftEdgeProfileHeight = jQuery('select[name="triangleEdgeProfile3"] option:selected').data('size');

							if ( triangleEdgeProfile3 != '' && triangleEdgeProfile3 != '0' && topLeftEdgeProfileHeight != '0' ) {

								var topLeftEdgeProfileValue = jQuery('select[name="triangleEdgeProfile3"] option:selected').text();

								shapesList.push(createEdgeProfileShape(

									{

										x: group.left + ( group.width / 2 ) - defaultGap - topLeftEdgeProfileHeight,

										y: group.top - ( topLeftEdgeProfileHeight / 2 ),

										width: topLeftEdgeProfileHeight,

										height: convertMmToPx(parseInt(group._objects[6].text.replace('mm', ''))),

										name: 'triangleEdgeProfile3',

										text: topLeftEdgeProfileValue,

										value: triangleEdgeProfile3,

										angle: group._objects[6].customAngle,

									}

								));



							}

							break;

					}



					// Updating Shape Number

					shapeNo++;

					

					canvas.getObjects().forEach(obj => {

						if ( obj.mainShape ) {

							let right = obj.left + obj.width;

							if ( right > editX ) {

								editX = right + defaultGap;

							}

							

// 							let bottom = obj.top + obj.height;

// 							if ( bottom > editY ) {

// 								editY = bottom + defaultGap;

// 							}

						}

					});

					

					if ( shapesList.length > 1 ) {

						// Create a single group with the main shape, text, and the corner blank shapes

						var finalGroup = new fabric.Group(shapesList, {

							left: editX,

							top: editY,

							lockScalingX: true,

							lockScalingY: true,

							id: shapeNo,

							hasControls: true,
							lockRotation: false,
							mainShape: true

						});

						
						// Add the group to the canvas

						canvas.add(finalGroup);
						
						// Customize controls to show only rotation handle (after adding to canvas)
						finalGroup.setControlsVisibility({
							mt: false, // middle top
							mb: false, // middle bottom
							ml: false, // middle left
							mr: false, // middle right
							bl: false, // bottom left
							br: false, // bottom right
							tl: false, // top left
							tr: false, // top right
							mtr: true  // middle top rotate - this is the rotation handle
						});
						
						// Additional settings to completely disable resize
						finalGroup.lockScalingX = true;
						finalGroup.lockScalingY = true;
						finalGroup.lockScalingFlip = true;
						
						// Force update of controls
						finalGroup.setCoords();
						canvas.requestRenderAll();
					
					// Update slab usage calculation and visualization
					calculateSlabUsage();
					updateSlabVisualization();

						canvas.renderAll();



						canvas.setActiveObject(finalGroup);

						checkShapeBounds(finalGroup); // Check bounds immediately after adding the shape

						// Add double-click event handler to open shape details modal (both left and right click)
						finalGroup.on('mousedblclick', function(e) {
							e.e.preventDefault();
							canvas.setActiveObject(finalGroup);
							jQuery('#toolbar .tools #info').trigger('click');
						});

						// Additional handler for right-click double-click
						let finalGroupClickCount = 0;
						let finalGroupClickTimer = null;
						finalGroup.on('mousedown', function(e) {
							finalGroupClickCount++;
							if (finalGroupClickCount === 1) {
								finalGroupClickTimer = setTimeout(function() {
									finalGroupClickCount = 0;
								}, 300);
							} else if (finalGroupClickCount === 2) {
								clearTimeout(finalGroupClickTimer);
								finalGroupClickCount = 0;
								// Double-click detected (left or right button)
								canvas.setActiveObject(finalGroup);
								jQuery('#toolbar .tools #info').trigger('click');
							}
						});

						// Ensure the group updates properly during dragging and scaling

						finalGroup.on('moving', function(e) {

							const now = Date.now();

							if (now - lastMoveTime > 50) {  // Adjust the delay as needed

								lastMoveTime = now;

								canvas.requestRenderAll();

							}

						});





						finalGroup.on('modified', function(e) {

							const obj = e.target;



							// Get proposed new position

							let newLeft = obj.left;

							let newTop = obj.top;



							// Check and adjust for overlaps

							const adjustedPosition = preventShapeOverlap(obj, newLeft, newTop);



							// Apply adjusted position

							obj.set({

								left: adjustedPosition.left,

								top: adjustedPosition.top

							});



							canvas.renderAll();



							cleanupEmptyInfoBoxes();



							// Check if we need new boxes

							checkShapeBounds(obj);

							

							saveState();

						});

					} else {



						group.set({

							left: editX,

							top: editY,

							id: shapeNo,

							hasControls: true,
							lockRotation: false,
							mainShape: true

						});

						
						canvas.renderAll();
						
						// Customize controls to show only rotation handle (after rendering)
						group.setControlsVisibility({
							mt: false, // middle top
							mb: false, // middle bottom
							ml: false, // middle left
							mr: false, // middle right
							bl: false, // bottom left
							br: false, // bottom right
							tl: false, // top left
							tr: false, // top right
							mtr: true  // middle top rotate - this is the rotation handle
						});
						
						// Additional settings to completely disable resize
						group.lockScalingX = true;
						group.lockScalingY = true;
						group.lockScalingFlip = true;
						
						// Force update of controls
						group.setCoords();
						canvas.requestRenderAll();



						canvas.setActiveObject(group);

						

						// Check if we need new boxes

						checkShapeBounds(group);

						// Add double-click event handler to open shape details modal (both left and right click)
						group.on('mousedblclick', function(e) {
							e.e.preventDefault();
							canvas.setActiveObject(group);
							jQuery('#toolbar .tools #info').trigger('click');
						});

						// Additional handler for right-click double-click
						let groupClickCount = 0;
						let groupClickTimer = null;
						group.on('mousedown', function(e) {
							groupClickCount++;
							if (groupClickCount === 1) {
								groupClickTimer = setTimeout(function() {
									groupClickCount = 0;
								}, 300);
							} else if (groupClickCount === 2) {
								clearTimeout(groupClickTimer);
								groupClickCount = 0;
								// Double-click detected (left or right button)
								canvas.setActiveObject(group);
								jQuery('#toolbar .tools #info').trigger('click');
							}
						});

						group.on('modified', function(e) {

							const obj = e.target;



							// Get proposed new position

							let newLeft = obj.left;

							let newTop = obj.top;



							// Check and adjust for overlaps

							const adjustedPosition = preventShapeOverlap(obj, newLeft, newTop);



							// Apply adjusted position

							obj.set({

								left: adjustedPosition.left,

								top: adjustedPosition.top

							});



							canvas.renderAll();



							cleanupEmptyInfoBoxes();



							// Check if we need new boxes

							checkShapeBounds(obj);

						});



						// Ensure the group updates properly during dragging and scaling

						group.on('moving', function(e) {

							const now = Date.now();

							if (now - lastMoveTime > 50) {  // Adjust the delay as needed

								lastMoveTime = now;

								canvas.requestRenderAll();

							}

						});



					}

					

					getTotalMM();



					saveState();

				});





				// Show custom notification if no object is selected

				function checkIfShapeSelected() {

					var activeObject = canvas.getActiveObject();

					if ( ! activeObject ) {

						showCustomAlert('Please select any shape first');

						return false;

					} else {

						return activeObject;

					}

				}

				// Function to show toolbar icons when shape is selected
				function showShapeToolbarIcons() {
					jQuery('#toolbar .tools #info').show();
					jQuery('#toolbar .tools #clone').show();
					jQuery('#toolbar .tools #delete').show();
					jQuery('#toolbar .tools #rotate').show();
				}

				// Function to hide toolbar icons when no shape is selected
				function hideShapeToolbarIcons() {
					jQuery('#toolbar .tools #info').hide();
					jQuery('#toolbar .tools #clone').hide();
					jQuery('#toolbar .tools #delete').hide();
					jQuery('#toolbar .tools #rotate').hide();
				}

				

				// Custom alert function that won't exit fullscreen

				function showCustomAlert(message) {

					// Create custom alert modal

					var alertModal = jQuery('<div class="custom-alert-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000002; justify-content: center; align-items: center;">' +

						'<div class="custom-alert-content" style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 400px; text-align: center;">' +

						'<p style="margin: 0 0 15px 0; font-size: 16px;">' + message + '</p>' +

						'<button class="custom-alert-ok" style="background: #0d6efd; color: white; border: none; padding: 8px 20px; border-radius: 3px; cursor: pointer;">OK</button>' +

						'</div>' +

					'</div>');

					

					// Add to body

					jQuery('body').append(alertModal);

					

					// Show modal

					alertModal.css('display', 'flex');

					

					// Handle OK button

					alertModal.find('.custom-alert-ok').click(function() {

						alertModal.remove();

					});

					

					// Handle backdrop click

					alertModal.click(function(e) {

						if (e.target === this) {

							alertModal.remove();

						}

					});

					

					// Auto close after 3 seconds

					setTimeout(function() {

						if (alertModal.length) {

							alertModal.remove();

						}

					}, 3000);

				}

				

				// Custom confirm function that won't exit fullscreen

				function showCustomConfirm(message, callback) {

					// Create custom confirm modal

					var confirmModal = jQuery('<div class="custom-confirm-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000002; justify-content: center; align-items: center;">' +

						'<div class="custom-confirm-content" style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 400px; text-align: center;">' +

						'<p style="margin: 0 0 15px 0; font-size: 16px;">' + message + '</p>' +

						'<button class="custom-confirm-yes" style="background: #dc3545; color: white; border: none; padding: 8px 20px; border-radius: 3px; cursor: pointer; margin-right: 10px;">Yes</button>' +

						'<button class="custom-confirm-no" style="background: #6c757d; color: white; border: none; padding: 8px 20px; border-radius: 3px; cursor: pointer;">No</button>' +

						'</div>' +

					'</div>');

					

					// Add to body

					jQuery('body').append(confirmModal);

					

					// Show modal

					confirmModal.css('display', 'flex');

					

					// Handle Yes button

					confirmModal.find('.custom-confirm-yes').click(function() {

						confirmModal.remove();

						if (callback) callback();

					});

					

					// Handle No button

					confirmModal.find('.custom-confirm-no').click(function() {

						confirmModal.remove();

					});

					

					// Handle backdrop click (acts as No)

					confirmModal.click(function(e) {

						if (e.target === this) {

							confirmModal.remove();

						}

					});

				}





				// Edit information of the selected object

				jQuery('#toolbar .tools #info').click(function() {

					var activeObject = checkIfShapeSelected();

					if ( activeObject && activeObject.type === 'group' ) {

						resetShapeModal();



						var obj = activeObject.getObjects();

						if ( ( obj[0].type == 'circle' || obj[0].type == 'ellipse' ) && obj[2].type == 'group' ) {

							var shapeName = obj[2]._objects[0].shapeName;

							jQuery('#shapeModal #shapeWidth').val(convertPxToMm(obj[2]._objects[0].width));

							jQuery('#shapeModal #shapeHeight').val(convertPxToMm(obj[2]._objects[0].height));

							jQuery('#shapeModal .edge-profile-section > div.' + shapeName).show();

							jQuery('#shapeModal #shapeName').val(obj[2]._objects[1].text && obj[2]._objects[1].text != 'Enter Name' ? obj[2]._objects[1].text : '');

							jQuery('#shapeModal .edge-profile-section > div.' + shapeName + ' select[name="' + obj[0].customName + '"]').val(obj[0].customValue).trigger('change');

						} else if ( obj[0].type == 'ellipse' && obj[2].type != 'group' ) {

							var shapeName = obj[0].shapeName;

							jQuery('#shapeModal #shapeWidth').val(convertPxToMm(obj[0].width));

							jQuery('#shapeModal #shapeHeight').val(convertPxToMm(obj[0].height));

							jQuery('#shapeModal .edge-profile-section > div.' + shapeName).show();

							jQuery('#shapeModal #shapeName').val(obj[1].text && obj[1].text != 'Enter Name' ? obj[1].text : '');

							var text = obj[4].text;

							if ( text  == 'Unfinished') {

								jQuery('#shapeModal .edge-profile-section > div.' + shapeName + ' select[name="' + shapeName + 'EdgeProfile1"]').val('0').trigger('change'); 

							} else {

								jQuery('#shapeModal .edge-profile-section > div.' + shapeName + ' select[name="' + shapeName + 'EdgeProfile1"]').val(text.replace(/ /g, '-')).trigger('change');

							}

						} else {

							if ( obj[0].type == 'group' ) {

								var obj = obj[0]._objects;

							}

							

							var shapeName = obj[0].shapeName;

							jQuery('#shapeModal #shapeWidth').val(convertPxToMm(obj[0].width));

							jQuery('#shapeModal #shapeHeight').val(convertPxToMm(obj[0].height));

							jQuery('#shapeModal .edge-profile-section > div.' + shapeName).show();

							jQuery('#shapeModal #shapeName').val(obj[1].text && obj[1].text != 'Enter Name' ? obj[1].text : '');



							var length = jQuery('#shapeModal .edge-profile-section > div.' + shapeName + ' select').length;

							for ( var i=1; i <= length; i++ ) {

								var text = obj[ ( i * 2 ) + 1 ].text;

								if ( text  == 'Unfinished') {

									jQuery('#shapeModal .edge-profile-section > div.' + shapeName + ' select#shapeEdgeProfile' + i + '').val('0').trigger('change'); 

								} else {

									jQuery('#shapeModal .edge-profile-section > div.' + shapeName + ' select#shapeEdgeProfile' + i + '').val(text.replace(/ /g, '-')).trigger('change');

								}

							}

						}

						

						if ( shapeName == 'circle' ) {

							jQuery('#shapeModal .form-group #shapeWidth').parent().hide();

						}



						// Hide the modal

						jQuery('#shapeModal #saveShapeDetails').attr('data-state', 'edit');

						jQuery('#shapeModal').css('display', 'flex').attr('data-shape', shapeName);

						

						// Maintain fullscreen when modal opens

						if (isFullscreen) {

							setTimeout(function() {

								var calculatorContainer = jQuery('.calculator-container');

								if (!calculatorContainer.hasClass('fullscreen-mode')) {

									calculatorContainer.addClass('fullscreen-mode');

								}

							}, 50);

						}

					}

				});



				

				var rotatedObjectList = {};

				

				// Rotate the selected object

				jQuery('#toolbar .tools #rotate').click(function() {

					var activeObject = checkIfShapeSelected();

					if ( activeObject ) {

						var adjustedPosition = {};

						if ( activeObject.angle == 0 ) {

							delete rotatedObjectList[activeObject.id];

							rotatedObjectList[activeObject.id] = {'left': activeObject.left, 'top': activeObject.top};

						}

						

						activeObject.rotate((activeObject.angle + 90) % 360);

						

						if ( activeObject.angle == 0 ) {

							adjustedPosition.left = rotatedObjectList[activeObject.id].left;

							adjustedPosition.top = rotatedObjectList[activeObject.id].top;

						} else if ( activeObject.angle == 90 ) {

							adjustedPosition.left = rotatedObjectList[activeObject.id].left + activeObject.height;

							adjustedPosition.top = rotatedObjectList[activeObject.id].top;

						} else if ( activeObject.angle == 180 ) {

							adjustedPosition.left = rotatedObjectList[activeObject.id].left + activeObject.width;

							adjustedPosition.top = rotatedObjectList[activeObject.id].top + activeObject.height;

						} else if ( activeObject.angle == 270 ) {

							adjustedPosition.left = rotatedObjectList[activeObject.id].left;

							adjustedPosition.top = rotatedObjectList[activeObject.id].top + activeObject.width;

						}

						

						activeObject.set({

							left: adjustedPosition.left,

							top: adjustedPosition.top

						});

						

						activeObject.setCoords();

						canvas.renderAll();



						checkShapeBounds(activeObject);

						

						// Save the initial state

						saveState();

					}

				});



				// Zoom in

				jQuery('#toolbar .tools #zoom-in').click(function() {

					zoomCanvas(true);

					saveState();

				});



				// Zoom out

				jQuery('#toolbar .tools #zoom-out').click(function() {

					zoomCanvas(false);

					saveState();

				});



				// Delete selected object

				jQuery('#toolbar .tools #delete').click(function() {

					var activeObject = checkIfShapeSelected();

					if (activeObject) {

						showCustomConfirm('Are you sure you want to delete this shape?', function() {

						if (activeObject.type === 'group') {

							// Ungroup all the objects from the group

							activeObject.forEachObject(function(object) {

								canvas.remove(object);  // Remove each child object from the canvas

							});

						}

						canvas.remove(activeObject);

						canvas.renderAll(); // Re-render the canvas

						cleanupEmptyInfoBoxes();

						// Force cleanup of all empty boxes after shape deletion

						setTimeout(function() {

							cleanupEmptyInfoBoxes();

						}, 100);

											getTotalMM();
					updateTotalMMDisplay();

					saveState();

						

						// Ensure fullscreen is maintained after operations

						if (isFullscreen) {

							setTimeout(function() {

								var calculatorContainer = jQuery('.calculator-container');

								if (!calculatorContainer.hasClass('fullscreen-mode')) {

									calculatorContainer.addClass('fullscreen-mode');

								}

							}, 100);

						}

						});

					}

				});

				// Clone selected object
				jQuery('#toolbar .tools #clone').click(function() {
					var activeObject = checkIfShapeSelected();
					if (activeObject) {
						// Clone the object
						activeObject.clone(function(clonedObj) {
							// Position the clone slightly offset from original
							clonedObj.set({
								left: activeObject.left + 20,
								top: activeObject.top + 20
							});
							
							// Add the clone to canvas
							canvas.add(clonedObj);
							canvas.setActiveObject(clonedObj);
							canvas.renderAll();
							
							// Ensure watermarks stay at the back
							ensureWatermarksAtBack();
							
							// Save state for undo/redo
							saveState();
							
							// Update bounds and scrollbars
							checkShapeBounds(clonedObj);
							expandCanvasIfNeeded();
							updateScrollbars();
							
							// Update slab usage calculation and visualization
							calculateSlabUsage();
							updateSlabVisualization();
						});
					}
				});



				// Fullscreen functionality

				var isFullscreen = false;

				

				jQuery('#toolbar .tools #fullscreen').click(function() {

					toggleFullscreen();

				});

				

				function toggleFullscreen() {

					var calculatorContainer = jQuery('.calculator-container');

					var fullscreenIcon = jQuery('#fullscreen');

					

					if (!isFullscreen) {

						// Enter native browser fullscreen

						var element = document.documentElement; // Use entire document

						

						if (element.requestFullscreen) {

							element.requestFullscreen();

						} else if (element.mozRequestFullScreen) { // Firefox

							element.mozRequestFullScreen();

						} else if (element.webkitRequestFullscreen) { // Chrome, Safari

							element.webkitRequestFullscreen();

						} else if (element.msRequestFullscreen) { // IE/Edge

							element.msRequestFullscreen();

						}

						

						// Apply fullscreen styling

						calculatorContainer.addClass('fullscreen-mode');

						fullscreenIcon.addClass('exit-fullscreen');

						fullscreenIcon.attr('title', 'Exit Fullscreen');

						isFullscreen = true;

						

						// Resize canvas and update rulers after entering fullscreen

						setTimeout(function() {

							canvas.calcOffset();

							canvas.renderAll();

							addRulers();

						}, 300);

						

					} else {

						// Exit native browser fullscreen

						if (document.exitFullscreen) {

							document.exitFullscreen();

						} else if (document.mozCancelFullScreen) { // Firefox

							document.mozCancelFullScreen();

						} else if (document.webkitExitFullscreen) { // Chrome, Safari

							document.webkitExitFullscreen();

						} else if (document.msExitFullscreen) { // IE/Edge

							document.msExitFullscreen();

						}

						

						// Remove fullscreen styling

						calculatorContainer.removeClass('fullscreen-mode');

						fullscreenIcon.removeClass('exit-fullscreen');

						fullscreenIcon.attr('title', 'Enter Fullscreen');

						isFullscreen = false;

						

						// Resize canvas and update rulers after exiting fullscreen

						setTimeout(function() {

							canvas.calcOffset();

							canvas.renderAll();

							addRulers();

						}, 300);

					}

				}

				

				// Listen for fullscreen change events

				document.addEventListener('fullscreenchange', handleFullscreenChange);

				document.addEventListener('webkitfullscreenchange', handleFullscreenChange);

				document.addEventListener('mozfullscreenchange', handleFullscreenChange);

				document.addEventListener('MSFullscreenChange', handleFullscreenChange);

				

				function handleFullscreenChange() {

					var calculatorContainer = jQuery('.calculator-container');

					var fullscreenIcon = jQuery('#fullscreen');

					

					// Check if we're in fullscreen mode

					var isInFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement || 

						document.mozFullScreenElement || document.msFullscreenElement);

					

					if (isInFullscreen && !isFullscreen) {

						// Entered fullscreen

						calculatorContainer.addClass('fullscreen-mode');

						fullscreenIcon.addClass('exit-fullscreen');

						fullscreenIcon.attr('title', 'Exit Fullscreen');

						isFullscreen = true;

						

						setTimeout(function() {

							canvas.calcOffset();

							canvas.renderAll();

							addRulers();

							updateScrollbars(); // Update scrollbars for fullscreen

							

							// Fix canvas interaction in fullscreen

							canvas.selection = false; // Keep selection disabled as set initially

							canvas.forEachObject(function(obj) {

								// Only make shapes selectable, not green boxes or watermarks

								if (!isGreenBox(obj) && !obj.isWatermark) {

									obj.selectable = true;

									obj.evented = true;

								} else {

									obj.selectable = false;

									obj.evented = false;

								}

							});

							canvas.calcOffset();

						}, 300);

						

					} else if (!isInFullscreen && isFullscreen) {

						// Exited fullscreen (could be via ESC key)

						calculatorContainer.removeClass('fullscreen-mode');

						fullscreenIcon.removeClass('exit-fullscreen');

						fullscreenIcon.attr('title', 'Enter Fullscreen');

						isFullscreen = false;

						

						setTimeout(function() {

							canvas.calcOffset();

							canvas.renderAll();

							addRulers();

							updateScrollbars(); // Update scrollbars when exiting fullscreen

							

							// Fix canvas interaction when exiting fullscreen

							canvas.forEachObject(function(obj) {

								// Only make shapes selectable, not green boxes or watermarks

								if (!isGreenBox(obj) && !obj.isWatermark) {

									obj.selectable = true;

									obj.evented = true;

								} else {

									obj.selectable = false;

									obj.evented = false;

								}

							});

							canvas.calcOffset();

						}, 300);

					}

				}

				

				// Handle ESC key to exit fullscreen

				jQuery(document).keydown(function(e) {

					if (e.keyCode === 27 && isFullscreen) { // ESC key

						toggleFullscreen();

					}

				});



				// Undo/Redo functionality

				let undoStack = [];

				let redoStack = [];

				let isUndoRedo = false; // Flag to track undo/redo operations



				// Define your custom properties here

				const customProperties = ['id', 'customName', 'customValue', 'customText', 'shapeName', 'mainShape']; // Add all your custom properties to this array



				// Save canvas state to undoStack on every change

				function saveState() {

					if (!isUndoRedo) { // Prevent saving state during undo/redo operations

						redoStack = []; // Clear the redo stack when a new change is made

						

						// Temporarily hide watermarks during state save

						const watermarks = [];

						canvas.getObjects().forEach(obj => {

							if (obj.isWatermark) {

								watermarks.push(obj);

								canvas.remove(obj);

							}

						});

						

						const json = canvas.toJSON(customProperties);

						undoStack.push(json);

						

						// Restore watermarks

						watermarks.forEach(watermark => {

							canvas.add(watermark);

							canvas.sendToBack(watermark);

						});

						canvas.renderAll();

					}

				}



				// Function to restore custom properties and apply specific rules

				function restoreCustomProperties() {

					canvas.getObjects().forEach(obj => {

						if ( !isGreenBox(obj) && ( obj.left < defaultGap || obj.top < defaultGap ) ) {

							canvas.remove(obj);

						}

						customProperties.forEach(prop => {

							if (obj[prop] !== undefined) {

								obj.set(prop, obj[prop]);

							}

						});



						// Apply specific rules (like for green boxes)

						if (isGreenBox(obj)) {

							obj.selectable = false;

						}

					});

				}



				jQuery('#toolbar .tools #undo').click(function() {

					if (undoStack.length > 1) {

						isUndoRedo = true; // Set flag to avoid triggering saveState

						redoStack.push(undoStack.pop()); // Move current state to redo stack

						const lastState = undoStack[undoStack.length - 1];

						canvas.loadFromJSON(lastState, function() {

							restoreCustomProperties();

							loadWatermark(); // Restore watermarks after undo

							canvas.renderAll();

						});

						isUndoRedo = false; // Reset flag after undo

					}

				});



				jQuery('#toolbar .tools #redo').click(function() {

					if (redoStack.length > 0) {

						isUndoRedo = true; // Set flag to avoid triggering saveState

						const state = redoStack.pop();

						undoStack.push(canvas.toJSON(customProperties)); // Push current state back to undo stack

						canvas.loadFromJSON(state, function() {

							restoreCustomProperties();

							loadWatermark(); // Restore watermarks after redo

							canvas.renderAll();

						});

						isUndoRedo = false; // Reset flag after redo

					}

				});



				// Save the initial state

				saveState();

				

				var isObjectMoving  = false;

				canvas.on('object:moving', function (event) {

					isObjectMoving = true;

					

					// Expand canvas in real-time while dragging

					const obj = event.target;

					if (obj && !obj.isWatermark && !isGreenBox(obj)) {

						const bounds = obj.getBoundingRect();

						const rightEdge = bounds.left + bounds.width + 100;

						const bottomEdge = bounds.top + bounds.height + 100;

						

						let needsUpdate = false;

						if (rightEdge > canvas.getWidth()) {

							canvas.setWidth(rightEdge);

							needsUpdate = true;

						}

						if (bottomEdge > canvas.getHeight()) {

							canvas.setHeight(bottomEdge);

							needsUpdate = true;

						}

						

						if (needsUpdate) {

							addRulers();

							updateScrollbars();

						}

					}

				});



				canvas.on('mouse:up', function (event) {

					if (isObjectMoving){

						isObjectMoving = false;

						cleanupEmptyInfoBoxes();

						// Clean up orphaned watermarks when dragging stops

						cleanupOrphanedWatermarks();

						saveState();

						expandCanvasIfNeeded(); // Expand canvas when objects are moved

						updateScrollbars(); // Update scrollbars when objects are moved

					} 

				});

				// Show/hide toolbar icons based on selection
				canvas.on('selection:created', function(e) {
					if (e.target && e.target.mainShape) {
						showShapeToolbarIcons();
					}
				});

				canvas.on('selection:updated', function(e) {
					if (e.target && e.target.mainShape) {
						showShapeToolbarIcons();
					} else {
						hideShapeToolbarIcons();
					}
				});

				canvas.on('selection:cleared', function() {
					hideShapeToolbarIcons();
				});

				// Handle when objects are removed from canvas
				canvas.on('object:removed', function(e) {
					// If the removed object was the active object, hide the toolbar icons
					if (canvas.getActiveObject() === null) {
						hideShapeToolbarIcons();
					}
				});

				// Handle when objects are modified (moved, resized, etc.)
				canvas.on('object:modified', function(e) {
					// Ensure toolbar icons are still visible if a shape is selected
					if (canvas.getActiveObject() && canvas.getActiveObject().mainShape) {
						showShapeToolbarIcons();
					}
				});





				jQuery('#toolbar .btns #download').click(function() {

					jQuery(this).next().slideToggle();

				});

				

				

				// Function to get canvas height and width for download

				function getCanvasDataForDownload() {

					let data = { width: 0, height: 0 };



					canvas.getObjects().forEach(obj => {

						if (isGreenBox(obj)) {

							data.width = Math.max(data.width, obj.left + obj.width);

							data.height = Math.max(data.height, obj.top + obj.height);

						}

					});

					

					data.width = data.width + 4;

					data.height = data.height + 4;

					

					return data;

				}



				// Function to prepare canvas for download

				function prepareCanvasForDownload(canvas) {

					// Deactivate all objects and store current state

					const activeObject = canvas.getActiveObject();

					const activeGroup = canvas.getActiveGroup?.();

// 					const activeGroup = canvas.getActiveGroup();



					if (activeObject) {

						canvas.discardActiveObject();

						// Hide toolbar icons when deselecting objects

						hideShapeToolbarIcons();

					}

					if (activeGroup) {

						canvas.discardActiveGroup();

					}



					canvas.renderAll();



					return { activeObject, activeGroup };

				}



				// Function to restore canvas state

				function restoreCanvasState(canvas, state) {

					if (state.activeObject) {

						canvas.setActiveObject(state.activeObject);

					}

					if (state.activeGroup) {

						canvas.setActiveGroup(state.activeGroup);

					}

					canvas.renderAll();

				}

					

				// Convert dataURL to Blob

				function dataURLToBlob(dataURL) {

					const byteString = atob(dataURL.split(',')[1]);

					const mimeString = dataURL.split(',')[0].split(':')[1].split(';')[0];

					const buffer = new Uint8Array(byteString.length);



					for (let i = 0; i < byteString.length; i++) {

						buffer[i] = byteString.charCodeAt(i);

					}



					return new Blob([buffer], { type: mimeString });

				}





				jQuery('#toolbar .btns ul.dropdown li').click(function() {

					const downloadType = jQuery(this).data('type');



					// Deselect objects before download

					const state = prepareCanvasForDownload(canvas);



					const data = getCanvasDataForDownload();



					// Store the current zoom level

					const originalZoom = canvas.getZoom();



					// Set zoom to 1 to get a 1:1 pixel ratio for the 100x100 area

					canvas.setZoom(1);



					// Convert the area to a data URL

					const dataURL = canvas.toDataURL({

						format: 'jpeg',

						quality: 1.0,

						multiplier: 2,

						width: data.width,

						height: data.height,

						enableRetinaScaling: true,

					});

						

					const blob = dataURLToBlob(dataURL);

					const blobURL = URL.createObjectURL(blob);



					if (downloadType === 'EMAIL') {

						// Open email modal instead of download

						jQuery('#emailModal').css('display', 'flex');

						

						// Maintain fullscreen when modal opens

						if (isFullscreen) {

							setTimeout(function() {

								var calculatorContainer = jQuery('.calculator-container');

								if (!calculatorContainer.hasClass('fullscreen-mode')) {

									calculatorContainer.addClass('fullscreen-mode');

								}

							}, 50);

						}

						

						// Clean up blob

						setTimeout(() => {

							URL.revokeObjectURL(blobURL);

						}, 100);

						

					} else if (downloadType === 'SAVE') {
						// Open save drawing modal
						jQuery('#saveDrawingModal').css('display', 'flex');
						
						// Maintain fullscreen when modal opens
						if (isFullscreen) {
							setTimeout(function() {
								var calculatorContainer = jQuery('.calculator-container');
								if (!calculatorContainer.hasClass('fullscreen-mode')) {
									calculatorContainer.addClass('fullscreen-mode');
								}
							}, 50);
						}
						
						// Clean up blob
						setTimeout(() => {
							URL.revokeObjectURL(blobURL);
						}, 100);
						
					} else if (downloadType === 'VIEW') {
						// Open view drawings modal
						jQuery('#viewDrawingsModal').css('display', 'flex');
						
						// Load saved drawings
						loadSavedDrawings();
						
						// Maintain fullscreen when modal opens
						if (isFullscreen) {
							setTimeout(function() {
								var calculatorContainer = jQuery('.calculator-container');
								if (!calculatorContainer.hasClass('fullscreen-mode')) {
									calculatorContainer.addClass('fullscreen-mode');
								}
							}, 50);
						}
						
						// Clean up blob
						setTimeout(() => {
							URL.revokeObjectURL(blobURL);
						}, 100);
						
					} else if (downloadType === 'PDF') {

						const { jsPDF } = window.jspdf;



						const pdf = new jsPDF({

							orientation: (data.height > data.width ? 'p' : 'l'),

							unit: 'px',

							format: [data.width, data.height],

							putOnlyUsedFonts: true,

							floatPrecision: 16

						});



						pdf.addImage(blobURL, 'JPEG', 0, 0, data.width, data.height);



						pdf.save('canvas-drawing.pdf');

						

						// Clean up

						setTimeout(() => {

							URL.revokeObjectURL(blobURL);

						}, 100);

					}



					canvas.setZoom(originalZoom);



					// Restore canvas state

					restoreCanvasState(canvas, state);

				});



				jQuery('form#email-form').on('submit', function(e) {

					e.preventDefault();



					const submitButton = jQuery(this).find('button[type="submit"]');

					const submitButtonText = submitButton.text();

					const errorContainer = jQuery(this).find('.error-message');

					const email = jQuery(this).find('#email').val();



					// Reset error messages

					errorContainer.html('').hide();



					// Add loading state

					submitButton.text('Sending...').prop('disabled', true);

					

					const { jsPDF } = window.jspdf;



					// Deselect objects before download

					const state = prepareCanvasForDownload(canvas);



					const data = getCanvasDataForDownload();



					// Store the current zoom level

					const originalZoom = canvas.getZoom();



					// Set zoom to 1 to get a 1:1 pixel ratio for the 100x100 area

					canvas.setZoom(1);



					// Convert the area to a data URL

					const dataURL = canvas.toDataURL({

						format: 'jpeg',

						quality: 1.0,

						multiplier: 2,

						width: data.width,

						height: data.height,

						enableRetinaScaling: true,

					});				



					// Create the PDF

					const pdf = new jsPDF({

						orientation: (data.height > data.width ? 'p' : 'l'),

						unit: 'px',

						format: [data.width, data.height],

						putOnlyUsedFonts: true,

						floatPrecision: 16

					});



					pdf.addImage(dataURL, 'JPEG', 0, 0, data.width, data.height);

					

					const pdfBlob = pdf.output('blob');





					canvas.setZoom(originalZoom);



					// Restore canvas state

					restoreCanvasState(canvas, state);



					// Get current total cutting MM
					getTotalMM(); // Ensure we have the latest calculations
					updateTotalMMDisplay();
					const totalCuttingMM = onlyCutAreaMM + mitredEdgeAreaMM;
					
					// Get slab name from URL parameters
					const urlParams = new URLSearchParams(window.location.search);
					const slabName = urlParams.get('name') || 'Custom Slab';
					
					// Create FormData
					const formData = new FormData();
					formData.append('action', 'send_html_email');
					formData.append('email', email);
					formData.append('pdf', pdfBlob, 'canvas.pdf'); // Name the file
					formData.append('slab_name', slabName);
					formData.append('total_cutting_mm', Math.round(totalCuttingMM));
					formData.append('only_cut_mm', Math.round(onlyCutAreaMM));
					formData.append('mitred_cut_mm', Math.round(mitredEdgeAreaMM));
					formData.append('slab_cost', '$' + slabCost.toLocaleString());
					formData.append('drawing_link', window.location.href); // Current page URL as drawing link





					jQuery.ajax({

						type: 'POST',

						url: '/wp-admin/admin-ajax.php', // WordPress AJAX endpoint

						data: formData,

						processData: false, // Required for FormData

						contentType: false, // Required for FormData

						success: function (response) {

							if (response.success) {

								submitButton.text('Sent');

								

								setTimeout( function(){

									jQuery('#emailModal').fadeOut();

									jQuery('#emailModal #email').val('');

									jQuery('#emailModal button[type="submit"]').html(submitButtonText).prop('disabled', false);
								}, 2000);

								

							} else {

								// Display specific error message from server
								const errorMessage = response.data && response.data.message ? response.data.message : 'Email failed to send!';
								errorContainer.html(errorMessage).show();
								submitButton.text(submitButtonText).prop('disabled', false);

							}

							

						},

						error: function () {

							errorContainer.html('Email failed to send! Please try again.').show();
							submitButton.text(submitButtonText).prop('disabled', false);

						},

					});

				});



				jQuery('#toolbar .btns #tutorial').click(function() {

					jQuery('#videoTutorialModal').css('display', 'flex');

					

					// Maintain fullscreen when modal opens

					if (isFullscreen) {

						setTimeout(function() {

							var calculatorContainer = jQuery('.calculator-container');

							if (!calculatorContainer.hasClass('fullscreen-mode')) {

								calculatorContainer.addClass('fullscreen-mode');

							}

						}, 50);

					}

				});

				// Auth Modal Functionality
				let isAuthenticated = false;
				let currentUserId = null; // Store the current user ID

				// Show auth modal or logout
				jQuery('#auth').click(function(e) {
					e.preventDefault();
					e.stopPropagation();
					console.log('Auth button clicked! Event:', e);
					
					if (isAuthenticated) {
						// User is logged in, show logout confirmation
						if (confirm('Are you sure you want to logout?')) {
							// Make AJAX request to logout
							jQuery.post(ajaxurl, {
								action: 'stone_slab_logout',
								nonce: nonce
							}, function(response) {
								try {
									var result = typeof response === 'string' ? JSON.parse(response) : response;
									
									if (result.success) {
										isAuthenticated = false;
										currentUserId = null; // Clear user ID on logout
										jQuery('#auth').css('opacity', '1');
										jQuery('#auth').attr('title', 'Click to login');
										jQuery('#auth').text('Login');
										
										// Clear form fields
										jQuery('#email').val('');
										jQuery('#password').val('');
										jQuery('#loginError').hide();
										
										alert('Logged out successfully!');
									} else {
										alert('Logout failed: ' + (result.message || 'Unknown error'));
									}
								} catch (e) {
									alert('An error occurred during logout');
								}
							}).fail(function() {
								alert('Network error during logout');
							});
						}
					} else {
						// User is not logged in, show login modal
						console.log('User not authenticated, showing login modal');
						console.log('Auth section element:', jQuery('#authSection'));
						console.log('Auth section current display:', jQuery('#authSection').css('display'));
						
						jQuery('#authSection').addClass('show');
						jQuery('#authSection').css('display', 'flex');
						
						console.log('Auth section after show:', jQuery('#authSection').css('display'));
						console.log('Auth section classes:', jQuery('#authSection').attr('class'));
					}
				});

				// Alternative event handler for debugging
				jQuery(document).on('click', '#auth', function(e) {
					e.preventDefault();
					e.stopPropagation();
					console.log('Auth button clicked via document.on!');
					
					if (!isAuthenticated) {
						console.log('Showing auth modal via alternative handler');
						jQuery('#authSection').addClass('show').css('display', 'flex');
					}
				});

				// Test click handler - should work regardless of toolbar state
				jQuery('#auth').on('click', function(e) {
					e.preventDefault();
					e.stopPropagation();
					console.log('Auth button clicked via direct handler!');
					alert('Auth button is working!');
					
					if (!isAuthenticated) {
						jQuery('#authSection').addClass('show').css('display', 'flex');
					}
				});

				// Tab switching functionality
				jQuery('#loginTab').click(function() {
					jQuery('.auth-tab').removeClass('active');
					jQuery('.auth-form').removeClass('active');
					jQuery(this).addClass('active');
					jQuery('#loginForm').addClass('active');
					jQuery('#authFooterText').html('Not a member yet? <a href="#" id="switchToRegister">Register now.</a>');
				});

				jQuery('#registerTab').click(function() {
					jQuery('.auth-tab').removeClass('active');
					jQuery('.auth-form').removeClass('active');
					jQuery(this).addClass('active');
					jQuery('#registerForm').addClass('active');
					jQuery('#authFooterText').html('Already have an account? <a href="#" id="switchToRegister">Login now.</a>');
				});

				// Switch between login and register
				jQuery('body').on('click', '#switchToRegister', function(e) {
					e.preventDefault();
					if (jQuery('#loginForm').hasClass('active')) {
						jQuery('#registerTab').click();
					} else {
						jQuery('#loginTab').click();
					}
				});

				// Login form submission
				jQuery('#loginForm').submit(function(e) {
					e.preventDefault();
					const email = jQuery('#email').val();
					const password = jQuery('#password').val();
					const errorElement = jQuery('#loginError');

					if (!email || !password) {
						errorElement.html('Please enter both email and password').show();
						return;
					}

					// Show loading state
					jQuery('#loginForm .auth-btn').text('Logging in...').prop('disabled', true);
					errorElement.hide();

					// Make AJAX request to WordPress login
					jQuery.post(ajaxurl, {
						action: 'stone_slab_login',
						email: email,
						password: password,
						nonce: nonce
					}, function(response) {
						try {
							var result = typeof response === 'string' ? JSON.parse(response) : response;
							
							if (result.success) {
								console.log('Login successful, setting isAuthenticated to true');
								isAuthenticated = true;
								
								// Store the user ID for future AJAX requests
								if (result.user && result.user.id) {
									currentUserId = result.user.id;
									console.log('Stored user ID:', currentUserId);
								}
								
								// Enable calculator functionality
								console.log('Calling enableCalculator()');
								enableCalculator();
								
								// Update UI to show logged in state
								jQuery('#auth').css('opacity', '0.7');
								jQuery('#auth').attr('title', 'Authenticated - Click to logout');
								jQuery('#auth').text('Logout');
								
								// Show success message and ensure modal is hidden after alert
								alert('Login successful! Welcome ' + email);
								// Force hide modal again after alert is dismissed
								forceHideAuthModal();
								
								// Additional explicit hiding of auth modal with timeout to ensure it's hidden
								console.log('Hiding auth modal explicitly');
								setTimeout(function() {
									forceHideAuthModal();
									console.log('Auth modal hidden after timeout');
								}, 100);
							} else {
								errorElement.html(result.message || 'Login failed').show();
							}
						} catch (e) {
							errorElement.html('An error occurred. Please try again.').show();
						}
						
						// Reset button state
						jQuery('#loginForm .auth-btn').text('Login').prop('disabled', false);
					}).fail(function() {
						errorElement.html('Network error. Please try again.').show();
						jQuery('#loginForm .auth-btn').text('Login').prop('disabled', false);
					});
				});

				// Register form submission
				jQuery('#registerForm').submit(function(e) {
					e.preventDefault();
					const username = jQuery('#reg_username').val();
					const email = jQuery('#reg_email').val();
					const password = jQuery('#reg_password').val();
					const confirmPassword = jQuery('#reg_confirm_password').val();
					const errorElement = jQuery('#registerError');

					if (!username || !email || !password || !confirmPassword) {
						errorElement.html('All fields are required').show();
						return;
					}

					if (password !== confirmPassword) {
						errorElement.html('Passwords do not match').show();
						return;
					}

					if (password.length < 6) {
						errorElement.html('Password must be at least 6 characters long').show();
						return;
					}

					// Show loading state
					jQuery('#registerForm .auth-btn').text('Registering...').prop('disabled', true);
					errorElement.hide();

					// Make AJAX request to WordPress registration
					jQuery.post(ajaxurl, {
						action: 'stone_slab_register',
						username: username,
						email: email,
						password: password,
						confirm_password: confirmPassword,
						nonce: nonce
					}, function(response) {
						try {
							var result = typeof response === 'string' ? JSON.parse(response) : response;
							
							if (result.success) {
								// VERIFICATION EMAIL TEMPORARILY DISABLED - Enable calculator directly
								// jQuery('#registerForm').hide();
								// jQuery('#emailVerificationMessage').show();
								// jQuery('#verificationEmail').text(jQuery('#reg_email').val());
								
								// Update footer text
								// jQuery('#authFooterText').html('Please check your email and click the verification link to activate your account.');
								
								// Show success message and enable calculator
								alert('Registration successful! You can now use the calculator.');
								enableCalculator();
							} else {
								errorElement.html(result.message || 'Registration failed').show();
								errorElement.css('background', '#f8d7da');
								errorElement.css('color', '#721c24');
								errorElement.css('border-color', '#f5c6cb');
							}
						} catch (e) {
							errorElement.html('An error occurred. Please try again.').show();
							errorElement.css('background', '#f8d7da');
							errorElement.css('color', '#721c24');
							errorElement.css('border-color', '#f5c6cb');
						}
						
						// Reset button state
						jQuery('#registerForm .auth-btn').text('Register').prop('disabled', false);
					}).fail(function() {
						errorElement.html('Network error. Please try again.').show();
						errorElement.css('background', '#f8d7da');
						errorElement.css('color', '#721c24');
						errorElement.css('border-color', '#f5c6cb');
						jQuery('#registerForm .auth-btn').text('Register').prop('disabled', false);
					});
				});

				// Close modal when clicking outside
				jQuery('#authSection').click(function(e) {
					if (e.target === this) {
						jQuery(this).removeClass('show').css('display', 'none');
					}
				});

				// Close modal with close button
				jQuery('#closeAuthModal').click(function() {
					jQuery('#authSection').removeClass('show').css('display', 'none');
				});

				// Logout button functionality
				jQuery('#logoutBtn').click(function() {
					if (confirm('Are you sure you want to logout?')) {
											// Make AJAX request to logout
					jQuery.post(ajaxurl, {
						action: 'stone_slab_logout',
						nonce: nonce
					}, function(response) {
							try {
								var result = typeof response === 'string' ? JSON.parse(response) : response;
								
								if (result.success) {
									isAuthenticated = false;
									jQuery('#auth').css('opacity', '1');
									jQuery('#auth').attr('title', 'Click to login');
									jQuery('#auth').text('Login');
									
									// Clear form fields
									jQuery('#email').val('');
									jQuery('#password').val('');
									jQuery('#loginError').hide();
									
									// Hide logout section and show register link
									jQuery('#logoutSection').hide();
									jQuery('#authFooterText').show();
									
									// Hide toolbar logout button
									jQuery('#toolbarLogoutBtn').hide();
									
									// Disable calculator
									disableCalculator();
									
									// Show auth modal for re-authentication
									jQuery('#authSection').addClass('show').css('display', 'flex');
									
									alert('Logged out successfully! Please login again to continue.');
								} else {
									alert('Logout failed: ' + (result.message || 'Unknown error'));
								}
							} catch (e) {
								alert('An error occurred during logout');
							}
						}).fail(function() {
							alert('Network error during logout');
						});
					}
				});

				// Toolbar logout button functionality
				jQuery('#toolbarLogoutBtn').click(function() {
					if (confirm('Are you sure you want to logout?')) {
						// Make AJAX request to logout
						jQuery.post(ajaxurl, {
							action: 'stone_slab_logout',
							nonce: nonce
						}, function(response) {
							try {
								var result = typeof response === 'string' ? JSON.parse(response) : response;
								
								if (result.success) {
									isAuthenticated = false;
									
									// Update auth button state
									jQuery('#auth').css('opacity', '1');
									jQuery('#auth').attr('title', 'Click to login');
									jQuery('#auth').text('Login');
									
									// Clear form fields
									jQuery('#email').val('');
									jQuery('#password').val('');
									jQuery('#loginError').hide();
									
									// Hide logout section and show register link
									jQuery('#logoutSection').hide();
									jQuery('#authFooterText').show();
									
									// Hide toolbar logout button
									jQuery('#toolbarLogoutBtn').hide();
									
									// Disable calculator
									disableCalculator();
									
									// Show auth modal for re-authentication
									jQuery('#authSection').addClass('show').css('display', 'flex');
									
									alert('Logged out successfully! Please login again to continue.');
								} else {
									alert('Logout failed: ' + (result.message || 'Unknown error'));
								}
							} catch (e) {
								alert('An error occurred during logout');
							}
						}).fail(function() {
							alert('Network error during logout');
						});
					}
				});

				// Email verification button handlers - TEMPORARILY DISABLED
				jQuery('#resendVerificationBtn').click(function() {
					var email = jQuery('#verificationEmail').text();
					
					// Make AJAX request to resend verification
					jQuery.post(ajaxurl, {
						action: 'stone_slab_resend_verification',
						email: email,
						nonce: nonce
					}, function(response) {
						try {
							var result = typeof response === 'string' ? JSON.parse(response) : response;
							
							if (result.success) {
								alert('Verification email resent successfully! Please check your inbox.');
							} else {
								alert('Failed to resend verification: ' + (result.message || 'Unknown error'));
							}
						} catch (e) {
							alert('An error occurred while resending verification');
						}
					}).fail(function() {
						alert('Network error while resending verification');
					});
				});

				jQuery('#backToLoginBtn').click(function() {
					// Reset to login form
					jQuery('#emailVerificationMessage').hide();
					jQuery('#registerForm').show();
					jQuery('#authFooterText').html('Not a member yet? <a href="#" id="switchToRegister">Register now.</a>');
					
					// Clear register form
					jQuery('#reg_username').val('');
					jQuery('#reg_email').val('');
					jQuery('#reg_password').val('');
					jQuery('#reg_confirm_password').val('');
					jQuery('#registerError').hide();
					
					// Switch to login tab
					jQuery('#loginTab').addClass('active');
					jQuery('#registerTab').removeClass('active');
					jQuery('#loginForm').addClass('active');
					jQuery('#registerForm').removeClass('active');
				});

				// Check authentication status on page load
				function checkAuthStatus() {
					jQuery.post(ajaxurl, {
						action: 'stone_slab_check_auth',
						nonce: nonce
					}, function(response) {
						try {
							var result = typeof response === 'string' ? JSON.parse(response) : response;
							
							if (result.success && result.authenticated) {
								isAuthenticated = true;
								jQuery('#auth').css('opacity', '0.7');
								jQuery('#auth').attr('title', 'Authenticated - Click to logout');
								jQuery('#auth').attr('src', './../assets/images/info.png');
								jQuery('#auth').attr('alt', 'Logged In');
								
								// User is authenticated, enable calculator
								enableCalculator();
							} else {
								// User not authenticated, show auth modal automatically
								isAuthenticated = false;
								console.log('User not authenticated, showing auth modal automatically');
								jQuery('#authSection').addClass('show').css('display', 'flex');
							}
						} catch (e) {
							console.log('Error checking auth status:', e);
							// On error, show auth modal
							isAuthenticated = false;
							jQuery('#authSection').addClass('show').css('display', 'flex');
						}
					}).fail(function() {
						console.log('Failed to check auth status');
						// On failure, show auth modal
						isAuthenticated = false;
						jQuery('#authSection').addClass('show').css('display', 'flex');
					});
				}

				// Check auth status when page loads
				checkAuthStatus();
				
				// Auto-show auth modal if user is not authenticated after a short delay
				setTimeout(function() {
					if (!isAuthenticated) {
						console.log('Auto-showing auth modal for unauthenticated user');
						jQuery('#authSection').addClass('show').css('display', 'flex');
					}
				}, 1000); // 1 second delay to ensure page is fully loaded

				// Function to calculate actual slab usage based on shape coverage
				function calculateSlabUsage() {
					// Get total area in mm²
					const totalAreaMM = totalMM;
					
					// Calculate single slab area in mm²
					const slabAreaMM = <?=$_GET['slab_width']?> * <?=$_GET['slab_height']?>;
					
					// Calculate how many slabs are actually needed
					const slabsNeeded = totalAreaMM / slabAreaMM;
					
					// Round to 1 decimal place for display
					const slabsNeededRounded = Math.round(slabsNeeded * 10) / 10;
					
					// Update the slab usage display - format as "1 Slab/s" for single slab
					if (slabsNeededRounded <= 1) {
						jQuery('#slabUsageDisplay').html('1 Slab/s');
					} else {
						jQuery('#slabUsageDisplay').html(slabsNeededRounded + ' Slab/s');
					}
					
					// Calculate total cutting area
					const totalCutting = onlyCutAreaMM + mitredEdgeAreaMM;
					jQuery('#totalCuttingDisplay').html(Math.round(totalCutting) + ' mm');
					
					// Calculate costs
					calculateCosts();
					
					console.log('Slab Usage Updated:', {
						totalAreaMM: totalAreaMM,
						slabAreaMM: slabAreaMM,
						slabsNeeded: slabsNeeded,
						slabsNeededRounded: slabsNeededRounded,
						totalCutting: totalCutting
					});
					
					return slabsNeededRounded;
				}
				
				// Function to calculate costs based on cutting areas
				function calculateCosts() {
					// Base slab cost (from URL parameter or default)
					const slabCost = 1000; // Default $1000, can be made dynamic
					
					// Production cost based on cutting areas (50.00 p/mm for only cut, 50.16 p/mm for mitred)
					const onlyCutCost = (onlyCutAreaMM * 50.00) / 1000; // Convert to dollars
					const mitredCutCost = (mitredEdgeAreaMM * 50.16) / 1000; // Convert to dollars
					const productionCost = onlyCutCost + mitredCutCost;
					
					// Installation cost (can be made configurable)
					const installationCost = 0; // Default $0, can be made dynamic
					
					// Total project cost
					const totalProjectCost = slabCost + productionCost + installationCost;
					
					// Update cost displays
					jQuery('#slabCostDisplay').html('$' + slabCost.toLocaleString());
					jQuery('#productionCostDisplay').html('$' + productionCost.toFixed(2));
					jQuery('#installationCostDisplay').html('$' + installationCost.toFixed(2));
					jQuery('#totalProjectCostDisplay').html('$' + totalProjectCost.toFixed(2));
					
					console.log('Costs Calculated:', {
						slabCost: slabCost,
						onlyCutCost: onlyCutCost,
						mitredCutCost: mitredCutCost,
						productionCost: productionCost,
						installationCost: installationCost,
						totalProjectCost: totalProjectCost
					});
				}

				// Function to update Total MM² display
				function updateTotalMMDisplay() {
					jQuery('#totalMMDisplay').html(totalMM.toLocaleString() + ' mm²');
					console.log('Total MM Updated:', totalMM);
				}

				// Function to visually shade used slabs based on shape placement
				function updateSlabVisualization() {
					// Get all shapes on canvas
					const shapes = canvas.getObjects().filter(obj => obj.mainShape);
					
					// For each green box, check if it contains shapes and shade accordingly
					infoBoxes.forEach((box, index) => {
						let hasShapes = false;
						let shapeArea = 0;
						
						// Check if this box contains any shapes
						shapes.forEach(shape => {
							if (checkIntersection(
								{
									left: shape.left,
									top: shape.top,
									right: shape.left + shape.width,
									bottom: shape.top + shape.height
								},
								{
									left: box.left,
									top: box.top,
									right: box.left + boxWidth,
									bottom: box.top + boxHeight
								}
							)) {
								hasShapes = true;
								// Calculate area of shape within this box
								shapeArea += shape.width * shape.height;
							}
						});
						
						// Update box appearance based on usage
						if (hasShapes) {
							// Calculate usage percentage
							const usagePercentage = Math.min(shapeArea / (boxWidth * boxHeight), 1);
							
							// Create gradient fill based on usage
							if (usagePercentage > 0.8) {
								// High usage - darker green
								box.set('fill', 'rgba(65, 186, 126, 0.3)');
								box.set('stroke', '#2d8a5a');
								box.set('strokeWidth', 3);
							} else if (usagePercentage > 0.5) {
								// Medium usage - medium green
								box.set('fill', 'rgba(65, 186, 126, 0.2)');
								box.set('stroke', '#41ba7e');
								box.set('strokeWidth', 2);
							} else if (usagePercentage > 0.2) {
								// Low usage - light green
								box.set('fill', 'rgba(65, 186, 126, 0.1)');
								box.set('stroke', '#41ba7e');
								box.set('strokeWidth', 2);
							} else {
								// Very low usage - very light green
								box.set('fill', 'rgba(65, 186, 126, 0.05)');
								box.set('stroke', '#41ba7e');
								box.set('strokeWidth', 1);
							}
						} else {
							// No shapes - transparent with light border
							box.set('fill', 'transparent');
							box.set('stroke', '#41ba7e');
							box.set('strokeWidth', 1);
						}
					});
					
					canvas.renderAll();
				}

				// Function to update slab usage visualization (alias for updateSlabVisualization)
				function updateSlabUsage() {
					updateSlabVisualization();
				}
				
				// Function to save drawing
				function saveDrawing() {
					const drawingName = jQuery('#drawing-name').val();
					const drawingNotes = jQuery('#drawing-notes').val();
					
					if (!drawingName) {
						alert('Please enter a drawing name');
						return;
					}
					
					// Generate PDF
					const { jsPDF } = window.jspdf;
					const pdf = new jsPDF({
						orientation: 'l',
						unit: 'mm',
						format: 'a4'
					});
					
					// Get canvas data
					const canvasData = canvas.toDataURL({
						format: 'jpeg',
						quality: 1.0,
						multiplier: 2
					});
					
					// Add image to PDF
					pdf.addImage(canvasData, 'JPEG', 10, 10, 190, 140);
					
					// Add drawing details
					pdf.setFontSize(12);
					pdf.text('Drawing Name: ' + drawingName, 10, 160);
					pdf.text('Total Cutting MM: ' + totalCuttingMM, 10, 170);
					pdf.text('Standard Cut MM: ' + onlyCutAreaMM, 10, 180);
					pdf.text('Mitred Cut MM: ' + mitredEdgeAreaMM, 10, 190);
					pdf.text('Slab Cost: $' + slabCost, 10, 200);
					
					if (drawingNotes) {
						pdf.text('Notes: ' + drawingNotes, 10, 210);
					}
					
					// Convert to blob and create file
					const pdfBlob = pdf.output('blob');
					const pdfFile = new File([pdfBlob], 'drawing_' + Date.now() + '.pdf', { type: 'application/pdf' });
					
					// Create FormData
					const formData = new FormData();
					formData.append('action', 'ssc_save_drawing');
					formData.append('nonce', nonce);
					
					// Add user ID if available
					if (currentUserId) {
						formData.append('user_id', currentUserId);
					}
					formData.append('drawing_name', drawingName);
					formData.append('drawing_notes', drawingNotes);
					formData.append('total_cutting_mm', totalCuttingMM);
					formData.append('only_cut_mm', onlyCutAreaMM);
					formData.append('mitred_cut_mm', mitredEdgeAreaMM);
					formData.append('slab_cost', '$' + slabCost);
					formData.append('pdf_file', pdfFile);
					formData.append('drawing_data', JSON.stringify({
						name: drawingName,
						notes: drawingNotes,
						total_cutting_mm: totalCuttingMM,
						only_cut_mm: onlyCutAreaMM,
						mitred_cut_mm: mitredEdgeAreaMM,
						slab_cost: slabCost,
						created_at: new Date().toISOString()
					}));
					formData.append('drawing_link', window.location.href);
					
					// Send AJAX request
					jQuery.ajax({
						url: ajaxurl,
						type: 'POST',
						data: formData,
						processData: false,
						contentType: false,
						success: function(response) {
							if (response.success) {
								alert('Drawing and PDF saved successfully!');
								jQuery('#saveDrawingModal').css('display', 'none');
								jQuery('#drawing-name').val('');
								jQuery('#drawing-notes').val('');
								
								// Provide view and download options
								if (response.data && response.data.pdf_filename) {
									let viewLink = ajaxurl + '?action=ssc_view_pdf&pdf=' + response.data.pdf_filename + '&nonce=' + nonce;
									let downloadLink = ajaxurl + '?action=ssc_download_pdf&pdf=' + response.data.pdf_filename + '&nonce=' + nonce;
									
									// Add user ID if available
									if (currentUserId) {
										viewLink += '&user_id=' + currentUserId;
										downloadLink += '&user_id=' + currentUserId;
									}
									
									const choice = confirm('Drawing saved! Click OK to view the PDF in browser, or Cancel to download it.');
									if (choice) {
										// View in browser
										window.open(viewLink, '_blank');
									} else {
										// Download
										window.open(downloadLink, '_blank');
									}
								}
							} else {
								alert('Failed to save drawing: ' + response.data);
							}
						},
						error: function() {
							alert('Error saving drawing');
						}
					});
				}
				
				// Function to load saved drawings
				function loadSavedDrawings() {
					const requestData = {
						action: 'ssc_get_drawings',
						nonce: nonce
					};
					
					// Add user ID if available
					if (currentUserId) {
						requestData.user_id = currentUserId;
					}
					
					jQuery.ajax({
						url: ajaxurl,
						type: 'POST',
						data: requestData,
						success: function(response) {
							if (response.success) {
								displaySavedDrawings(response.data);
							} else {
								jQuery('#saved-drawings-list').html('<p>Error loading drawings: ' + response.data + '</p>');
							}
						},
						error: function() {
							jQuery('#saved-drawings-list').html('<p>Error loading drawings</p>');
						}
					});
				}
				
				// Function to display saved drawings
				function displaySavedDrawings(drawings) {
					if (drawings.length === 0) {
						jQuery('#saved-drawings-list').html('<p>No saved drawings found.</p>');
						return;
					}
					
					let html = '<div class="drawings-grid">';
					drawings.forEach(function(drawing) {
						html += '<div class="drawing-item">';
						html += '<h4>' + (drawing.drawing_name || 'Untitled Drawing') + '</h4>';
						if (drawing.drawing_notes) {
							html += '<p><strong>Notes:</strong> ' + drawing.drawing_notes + '</p>';
						}
						html += '<p><strong>Total Cutting:</strong> ' + drawing.total_cutting_mm + ' mm</p>';
						html += '<p><strong>Standard Cut:</strong> ' + drawing.only_cut_mm + ' mm</p>';
						html += '<p><strong>Mitred Cut:</strong> ' + drawing.mitred_cut_mm + ' mm</p>';
						html += '<p><strong>Slab Cost:</strong> ' + drawing.slab_cost + '</p>';
						html += '<p><strong>Created:</strong> ' + new Date(drawing.created_at).toLocaleDateString() + '</p>';
						html += '<div class="drawing-actions">';
						let viewUrl = ajaxurl + '?action=ssc_view_pdf&pdf=' + drawing.pdf_file_path + '&nonce=' + nonce;
						let downloadUrl = ajaxurl + '?action=ssc_download_pdf&pdf=' + drawing.pdf_file_path + '&nonce=' + nonce;
						
						// Add user ID if available
						if (currentUserId) {
							viewUrl += '&user_id=' + currentUserId;
							downloadUrl += '&user_id=' + currentUserId;
						}
						
						html += '<a href="' + viewUrl + '" target="_blank" class="button">View PDF</a> ';
						html += '<a href="' + downloadUrl + '" class="button">Download PDF</a> ';
						html += '<button onclick="deleteDrawing(' + drawing.id + ')" class="button button-delete">Delete</button>';
						html += '</div>';
						html += '</div>';
					});
					html += '</div>';
					
					jQuery('#saved-drawings-list').html(html);
				}
				
				// Function to delete drawing
				function deleteDrawing(drawingId) {
					if (confirm('Are you sure you want to delete this drawing?')) {
						const requestData = {
							action: 'ssc_delete_drawing',
							nonce: nonce,
							drawing_id: drawingId
						};
						
						// Add user ID if available
						if (currentUserId) {
							requestData.user_id = currentUserId;
						}
						
						jQuery.ajax({
							url: ajaxurl,
							type: 'POST',
							data: requestData,
							success: function(response) {
								if (response.success) {
									alert('Drawing deleted successfully!');
									loadSavedDrawings(); // Reload the list
								} else {
									alert('Failed to delete drawing: ' + response.data);
								}
							},
							error: function() {
								alert('Error deleting drawing');
							}
						});
					}
				}
				
				// Add event handlers for new modals
				jQuery(document).ready(function() {
					// Save drawing form submit
					jQuery('#save-drawing-form').on('submit', function(e) {
						e.preventDefault();
						saveDrawing();
					});
					
					// Modal close buttons
					jQuery('#cancel-save').click(function() {
						jQuery('#saveDrawingModal').css('display', 'none');
					});
					
					jQuery('#cancel-view').click(function() {
						jQuery('#viewDrawingsModal').css('display', 'none');
					});
				});

			});

		</script>

	</body>

</html>